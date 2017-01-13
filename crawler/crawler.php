<?php
include 'db.php';

class Crawler {
	private $root_url;
	private $current_url;

	// public function __construct(){

	// 	return new Crawler;

	// }

	public function setUrl($url){
		$parsed_url = parse_url($url);
		$root = $parsed_url['scheme'] . '://' . $parsed_url['host'] . '/';
		$this->root_url = $root;
	}

	public function setCurrentUrl($url){
		$this->current_url = $url;
	}

	public function getLinks($url, $conn){
		$input = @file_get_contents($url);
		$regex = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		preg_match_all("/$regex/siU", $input, $matches);
		$l = $matches[2];
		$this->setIndexed($url, $conn);
		return $l;
	}

	public function getMetaTags(){
		$url = str_replace('&amp;', '&', $this->current_url);
		$input = get_meta_tags($url);
		return $input;
	}


	public static function getParagraphs($url){
		$input = @file_get_contents($url);
		$regex = "/<p>(.*)<\/p>/";
		preg_match_all($regex, $input, $matches);
		// $l = $matches[2];
		// $this->setIndexed($url, $conn);
		return $matches[1];
		
	}

	public static function getTable($url){
		$input = @file_get_contents($url);
		$regex = "/<td>(.*)<\/td>/";
		preg_match_all($regex, $input, $matches);
		// $l = $matches[2];
		// $this->setIndexed($url, $conn);
		return $matches;
		
	}

	public static function getHeadings($url){
		$input = @file_get_contents($url);
		$regex = "/<h1>(.*)<\/h1>/";
		preg_match_all($regex, $input, $matches);
		// $l = $matches[2];
		// $this->setIndexed($url, $conn);
		return $matches[1];
		
	}

	public function filterExternalLinks($links){
		$all_links = array();
		foreach ($links as $link) {
			
			if(substr($link, 0, 12) == "http://local"){

				array_push($all_links, $link);
			}

		}

		$output = array_unique($all_links);
		return $output;
	}


	public function filterInternalLinks($links){
		$all_links = array();
		foreach ($links as $link) {

		if(!(substr($link, 0, 8) == "https://") and !(substr($link, 0, 7) == "http://") and !(strpos($link, '#'))){

				$link = $this->modifyLink($link);
				array_push($all_links, $link);

			}
		}

		$output = array_unique($all_links);
		return $output;

	}

	public function save($links, $conn){

		$stmt = $conn->prepare("INSERT INTO urls (`link`) VALUES (?)");

		$stmt->bind_param("s", $url);

		foreach ($links as $link) {
			if(!$this->exist($link,$conn)){
				$url = $link;
				$stmt->execute();
			}
		}

	}

	public function exist($url, $conn){
		$sql = 'SELECT id FROM urls WHERE link = "'.$url.'"';
		// $stmt = $conn->prepare("SELECT id FROM urls where link = ?");

		$result = $conn->query($sql);

		 if($result->num_rows > 0){
		 	return true;
		 }

		 return false;
	}


	public function modifyLink($link){
		$go_back = substr_count($link,'../');
	
		if($go_back > 0){
			$url = explode('/', $this->current_url);
	
			for($i = $go_back; $i >= 0; $i--){
				array_pop($url);
			}
	
			$ln = '';
	
			foreach ($url as $key) {
				$ln .= $key . '/'; 
			}
	
			$ln .= substr($link, 3*$go_back);
	
			return str_replace("'", "", $ln);
		}

		if($go_back == 0){
			if(strpos($this->current_url, '.html')){
				$url = explode('/', $this->current_url);
	
				array_pop($url);
				
				$veza = '';

				foreach ($url as $key) {
					$veza .= $key . '/'; 

				}
				return str_replace("'", "", $veza . $link);
			}
		}

		// if(true){
		// 	$lnk = str_replace("'", "", $link);
		// 	// $br = count($link);
		// 	// $lnk = substr($link, 2);
		// 	return $this->current_url . $lnk;
		// }


		return str_replace("'", "", $this->current_url . $link);
	}

	public function setIndexed($url, $conn){
		// $sql = 'UPDATE urls SET indexed = 1 WHERE link = "$url"';
		// $conn->query($sql);
		// print_r($sql);

		$stmt1 = $conn->prepare("UPDATE urls SET indexed=? WHERE link=?");
		$stmt1->bind_param('is', $indexed, $ur);
		$indexed = 1;
		$ur = $url;
		$stmt1->execute();

	}

	public function saveMeta($tags, $id, $conn){
		$stmt = $conn->prepare("INSERT INTO meta_tags (`url_id`, `tag_name`, `tag_value`) VALUES (?, ?, ?)");

		$stmt->bind_param("iss", $url_id, $tag_name, $tag_value);

		foreach ($tags as $key => $value) {
			if($key == 'keywords' or $key == 'description' or $key == 'title' or $key == 'author'){
				$url_id = $id;
				$tag_name = $key;
				$tag_value = $value;
				$stmt->execute();
			}
		}

		$stmt1 = $conn->prepare("UPDATE urls SET indexed_meta=? WHERE id=?");
		$stmt1->bind_param('is', $indexed_meta, $link_id);
		$indexed_meta = 1;
		$link_id = $id;
		$stmt1->execute();
	}

	public function saveParagraphs($paragraphs, $id, $conn){
		$stmt = $conn->prepare("INSERT INTO paragraphs (`url_id`, `paragraph`) VALUES (?, ?)");

		$stmt->bind_param("is", $url_id, $paragraph);

		foreach ($paragraphs as $para) {
				$para = strip_tags($para);
				$url_id = $id;
				$paragraph = $para;
				$stmt->execute();
			
		}

		$stmt1 = $conn->prepare("UPDATE urls SET indexed_parag=? WHERE id=?");
		$stmt1->bind_param('is', $indexed_meta, $link_id);
		$indexed_meta = 1;
		$link_id = $id;
		$stmt1->execute();
	}

	public function saveHeadings($headings, $id, $conn){
		$stmt = $conn->prepare("INSERT INTO headings (`url_id`, `body`) VALUES (?, ?)");

		$stmt->bind_param("is", $url_id, $text);

		foreach ($headings as $h1) {
				$h1 = strip_tags($h1);
				$url_id = $id;
				$text = $h1;
				$stmt->execute();
			
		}

		$stmt1 = $conn->prepare("UPDATE urls SET indexed_h1=? WHERE id=?");
		$stmt1->bind_param('is', $indexed_h1, $link_id);
		$indexed_h1 = 1;
		$link_id = $id;
		$stmt1->execute();
	}


}