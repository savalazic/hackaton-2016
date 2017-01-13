<?php
include 'crawler.php';
include 'db.php';
		
		ini_set('max_execution_time', 0);

		$sql = "SELECT id, link FROM urls WHERE indexed_parag = 0";
		$result = $conn->query($sql);
		$links = array();
		if($result->num_rows > 0){
			$i = 0;
			while($row = $result->fetch_assoc()) {
		        $links[$i]['id'] = $row['id'];
		        $links[$i]['link'] = $row['link'];
		        $i++;
		    }
		}
		
		// }
		// echo "<pre>";
		// 	print_r($links);
		// 	echo "</pre>";
		foreach ($links as $link) {

			$crawler = new Crawler();
			$id = $link['id'];
			$crawler->setCurrentUrl($link['link']);
			$url = $link['link'];
			$paragraphs = $crawler->getParagraphs($url);

			$crawler->saveParagraphs($paragraphs, $id, $conn);

			// echo "<pre>";
			// print_r($paragraphs);
			// echo "</pre>";
			
		
		}
