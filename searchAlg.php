<?php 
include 'db.php';
// $searchInput = $_GET['name'];
$searchInput = $_GET['searchInput'];
$root_urls = [
	'http://localimdb.com/',
	'http://localserbia.travel/',
	'http://localtheonion.com/',
	'http://localsymfony.com/',
	'http://localbyteout.com/',
	'http://localeestec.rs/',
	'http://localraywenderlich.com/',
	'http://localthescienceexplorer.com/',
	'http://localkickstarter.com/'
	];

$searchInput = explode(' ', $searchInput);

$fullTextOutput = fullTextSearch($conn, $searchInput);
$keywordsOutput = keywordsSearch($conn, $searchInput);
$descriptionOutput = descriptionSearch($conn, $searchInput);
$titleOutput = titleSearch($conn, $searchInput);
$urlOutput = searchUrl($conn, $searchInput, $root_urls);
$headingsOutput = hedingsSearch($conn, $searchInput);



$points = array();
// $points[0]['url_id']= "";
// $points[0]['points']= 0;

$i = 0;
foreach ($fullTextOutput as $id) {
	if(in_array($id, $keywordsOutput)){
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] = 7;
	}else{
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] = 0;
	}

	if(in_array($id, $headingsOutput)){
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 6;
	}else{
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 0;
	}

	if(in_array($id, $descriptionOutput)){
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 5;
	}else{
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 0;
	}

	if(in_array($id, $titleOutput)){
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 3;
	}else{
		$points[$i]['url_id'] = $id;
		$points[$i]['points'] += 0;
	}

	$i++;
}

// array_merge($points)

// print_r($descriptionOutput);
// print_r($titleOutput);
$x = count($points);
if(isset($urlOutput)){
	$points[$x]['url_id'] = $urlOutput;
	$points[$x]['points'] = 1000;
}

usort($points, function($a, $b) {
    return $b['points'] - $a['points'];
});



// print_r($points);






function fullTextSearch($conn, $input){
	$sql = "SELECT url_id FROM paragraphs WHERE MATCH(paragraph) Against('";

		foreach ($input as $arsa) {
			$sql .=  str_replace('"', '', $arsa) ." OR" ;
		}

	$sql .= "null' IN BOOLEAN MODE)";

	$result = $conn->query($sql);
	$output = array();
	 while($row = $result->fetch_assoc()) {
       array_push($output, $row['url_id']);
    }

	return $output;
}


function keywordsSearch($conn, $searchInput){
	$output = array();
	foreach ($searchInput as $input) {
		$input = str_replace('"', '', $input);	
		$sql = "SELECT url_id FROM meta_tags WHERE tag_name = 'keywords' and tag_value like '%$input%' LIMIT 50";
		// echo $sql;
		$result = $conn->query($sql);
		
		 while($row = $result->fetch_assoc()) {
   	    array_push($output, $row['url_id']);
   	 }
	}
	return $output;
}

function descriptionSearch($conn, $searchInput){
	$output = array();
	foreach ($searchInput as $input) {
		$input = str_replace('"', '', $input);	
		$sql = "SELECT url_id FROM meta_tags WHERE tag_name = 'description' and tag_value like '%$input%' LIMIT 50";
		// echo $sql;
		$result = $conn->query($sql);
		
		 while($row = $result->fetch_assoc()) {
   	    array_push($output, $row['url_id']);
   	 }
	}
	return $output;
}

function titleSearch($conn, $searchInput){
	$output = array();
	foreach ($searchInput as $input) {
		$input = str_replace('"', '', $input);	
		$sql = "SELECT url_id FROM meta_tags WHERE tag_name = 'title' and tag_value like '%$input%' LIMIT 50";
		// echo $sql;
		$result = $conn->query($sql);
		
		while($row = $result->fetch_assoc()) {
   	    	array_push($output, $row['url_id']);
   	 	}
	}
	return $output;
}

function hedingsSearch($conn, $searchInput){
	$output = array();
	foreach ($searchInput as $input) {
		$input = str_replace('"', '', $input);	
		$sql = "SELECT url_id FROM headings WHERE body like '%$input%' LIMIT 50";
		// echo $sql;
		$result = $conn->query($sql);
		
		while($row = $result->fetch_assoc()) {
   	    	array_push($output, $row['url_id']);
   	 	}
	}
	return $output;
}

function searchUrl($conn, $searchInput, $root_urls){
	$output = array();
	$id = -1;
	foreach ($searchInput as $input) {
		$input = str_replace('"', '', $input);
		$sql = "SELECT id, link FROM urls WHERE link like '%$input%' order by link asc LIMIT 1";
		// echo $sql;
		$result = $conn->query($sql);

		while($row = $result->fetch_assoc()) {
   	    	$output[$row['id']] = $row['link'];
   	    	$id = $row['id'];
   	 	}

   	 	if(in_array(reset($output), $root_urls)){
   	 		return $id;
   	 	}
   	 	

	}
}

// function createArrayForJson($points, $hard_search, $conn){
// 	$output1 = array();
// 	$i = 0;
// 	foreach ($points as $key => $value){
// 		$sql = "SELECT headings.body, meta_tags.tag_value, urls.link, paragraphs.paragraph FROM urls LEFT JOIN meta_tags ON meta_tags.url_id = urls.id LEFT JOIN paragraphs ON paragraphs.url_id = urls.id LEFT JOIN headings ON headings.url_id = urls.id WHERE meta_tags.tag_name='title' and urls.id=$value[url_id] GROUP BY urls.link";
// 		$result = $conn->query($sql);

// 		while($row = $result->fetch_assoc()){
// 			$output1[$i]['title'] = $row['tag_value'];
// 			$output1[$i]['url'] = $row['link'];
// 			$output1[$i]['text'] = $row['paragraph'];
// 			$output1[$i]['h1'] = $row['body'];
// 			$i++;
// 		}

// 	}

// 	$output2 = array();
// 	$j = 0;
// 	foreach ($hard_search as $value){
// 		$sql = "SELECT headings.body, meta_tags.tag_value, urls.link, paragraphs.paragraph FROM urls LEFT JOIN meta_tags ON meta_tags.url_id = urls.id LEFT JOIN paragraphs ON paragraphs.url_id = urls.id LEFT JOIN headings ON headings.url_id = urls.id WHERE meta_tags.tag_name='title' and urls.id=$value GROUP BY urls.link";
// 		$result = $conn->query($sql);

// 		while($row = $result->fetch_assoc()){
// 			$output2[$j]['title'] = $row['tag_value'];
// 			$output2[$j]['url'] = $row['link'];
// 			$output2[$j]['text'] = $row['paragraph'];
// 			$output2[$j]['h1'] = $row['body'];
// 			$j++;
// 		}


// 	}

// 	$out = array_merge($output1, $output2);

// 	return $out;
// }



function createArrayForJson($points, $hard_search, $conn){
	$output1 = array();
	$i = 0;
	foreach ($points as $key => $value){
		$sql1 = "SELECT tag_value FROM meta_tags WHERE tag_name = 'title' and url_id = $value[url_id] LIMIT";
		$sql2 = "SELECT link FROM urls WHERE id = $value[url_id] LIMIT";
		$sql3 = "SELECT paragraph FROM paragraphs WHERE url_id = $value[url_id] LIMIT";
		$sql4 = "SELECT body FROM headings WHERE url_id = $value[url_id] LIMIT";

		$result1 = $conn->query($sql1);
		$result2 = $conn->query($sql2);
		$result3 = $conn->query($sql3);
		$result4 = $conn->query($sql4);

		$row1 = $result1->fetch_assoc();
		$row2 = $result2->fetch_assoc();
		$row3 = $result3->fetch_assoc();
		$row4 = $result4->fetch_assoc();

		$output1[$i]['title'] = $row1['tag_value'];
		$output1[$i]['url'] = $row2['link'];
		$output1[$i]['text'] = $row3['paragraph'];
		$output1[$i]['h1'] = $row4['body'];

		$i++;
		

	}

		$output2 = array();
		$j = 0;
	foreach ($hard_search as $value){
		$sql1 = "SELECT tag_value FROM meta_tags WHERE tag_name = 'title' and url_id = $value";
		$sql2 = "SELECT link FROM urls WHERE id = $value";
		$sql3 = "SELECT paragraph FROM paragraphs WHERE url_id = $value";
		$sql4 = "SELECT body FROM headings WHERE url_id = $value";

		$result1 = $conn->query($sql1);
		$result2 = $conn->query($sql2);
		$result3 = $conn->query($sql3);
		$result4 = $conn->query($sql4);

		$row1 = $result1->fetch_assoc();
		$row2 = $result2->fetch_assoc();
		$row3 = $result3->fetch_assoc();
		$row4 = $result4->fetch_assoc();

		$output1[$j]['title'] = $row1['tag_value'];
		$output1[$j]['url'] = $row2['link'];
		$output1[$j]['text'] = $row3['paragraph'];
		$output1[$j]['h1'] = $row4['body'];

		$j++;
		

	}

	$out = array_merge($output1, $output2);

	return $out;
}





$a1 = array_merge($fullTextOutput, $keywordsOutput);
$a2 = array_merge($a1, $descriptionOutput);
$a3 = array_merge($a2, $titleOutput);
$a4 = array_merge($a3, $headingsOutput);
$a5 = array_unique($a4);

// print_r($a5);
// print_r($headingsOutput);

$result = createArrayForJson($points, $a5, $conn);
// print_r($json);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1.0">
    <title>Woogla | Search</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="public/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto:300,400,500" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

  <nav>
        <div class="container-fluid">
          <a class="navbarBrand" href="index.php"><img src="public/img/logo.png" alt=""></a>
          <a class="navbarMenu" href="#"><i class="fa fa-th" aria-hidden="true"></i></a>
        </div>
      </nav>

      <main>

        <section class="searchHeader">
          <div class="container">
            <form class="searchForm" action="">
              <input class="searchInput" id="search" type="text" value="hackaton">
              <button class="searchButton--icon"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
          </div>
        </section>

        <section class="searchMain">
          <div class="container">
            <div class="searchResults">
              <p>5 results (0.44 seconds)</p>
            </div>

            <div class="searchItems">
              <?php foreach ($result as $key => $value) {
              	// echo $value['title'];

             echo '<div class="searchItem">
                <h2><a href="#">Hackaton</a></h2>
                <p class="searchItem-link">https://en.wikipedia.org/wiki/Hackathon</p>
                <p class="searchItem-desc">
                  A hackathon (also known as a hack day, hackfest or codefest) is a design sprint-like event in which computer programmers and others involved in software development, including graphic designers, interface designers, project managers, and others, often including subject-matter-experts, collaborate intensively on ...
                </p>
              </div>';
              }
              ?>
            </div>


          </div>
        </section>

      </main>

      <footer>
        <div class="container-fluid">
          <a class="footerAbout" href="#">About</a>
          <a class="footerSettings" href="#">Settings</a>
        </div>
      </footer>

  <script src="public/js/bundle.js"></script>
</body>
</html>

