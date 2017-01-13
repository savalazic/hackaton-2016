<?php
include_once('simple_html_dom.php');


$target_url = "http://www.tibia.com/community/?subtopic=characters&name=Nikla";

$html = new simple_html_dom();
$html->load_file($target_url);

$count = 0;

foreach($html->find('//*[@id="characters"]/div[5]/div/div/table[1]/tbody/tr['.$count.']') as $link){

	echo $link."<br />";
	$count++;
}

?>