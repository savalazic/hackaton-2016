<?php 

public function getLinks($url){
	$input = @file_get_contents($url);
	$regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	preg_match_all("/$regex/siU", $input, $matches);
	$l = $matches[2];
	return $l;
}

print_r(getLinks('http://localbyteout.com/'));