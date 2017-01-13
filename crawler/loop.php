<?php
include 'crawler.php';
include 'db.php';
	
	
		ini_set('max_execution_time', 0);

		$sql = "SELECT link FROM urls WHERE indexed = 0 ORDER BY id desc";
		$result = $conn->query($sql);
		$links = array();
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) {
		        array_push($links, $row['link']);
		    }
		
		}
		print_r($links);
		foreach ($links as $link) {
			$crawler = new Crawler();
			$crawler->setCurrentUrl($link);
			
			$urls = $crawler->getLinks($link, $conn);
			$ex = $crawler->filterExternalLinks($urls);
			$inter = $crawler->filterInternalLinks($urls);

			echo "<pre>";
			print_r($ex);
			echo "</pre>";
			
			echo "<pre>";
			print_r($inter);
			echo "</pre>";

			$crawler->save($ex, $conn);
			$crawler->save($inter, $conn);
		}
