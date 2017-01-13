<?php
include_once('simple_html_dom.php');
$target_url = "http://www.fiba.com/olympics/2016/2108/Serbia-USA#tab=boxscore_statistics";
$html = new simple_html_dom();
$html->load_file($target_url);
$count = 0;

echo "<table>";

	
	foreach($html->find('//*[@id="boxscore_pages_statistics_content"]/div[1]/table/tbody/tr['.
  	$count.']') as $link)
	{	
		echo "<tr>";
		echo "<td>";
	 	echo $link;
	 	echo "</td>";
	 	echo "</tr>";
		$count++;
	}
	

echo "</table>";
?>