<?php
include 'crawler.php';
include 'db.php';

$crawler = new Crawler();
$url = 'http://www.rezultati.com/';

$crawler->setCurrentUrl($url);

$links = $crawler->getLinks($url, $conn);
$ex = $crawler->filterExternalLinks($links);
$inter = $crawler->filterInternalLinks($links);
// $crawler->save($ex, $conn);
// $crawler->save($inter, $conn);


// echo "<pre>";
// print_r($ex);
// echo "</pre>";

// echo "<pre>";
// print_r($inter);
// echo "</pre>";

$table = $crawler->getParagraphs($url);

echo "<pre>";
print_r($table);
echo "</pre>";

// echo $crawler->modifyLink("../../index.html@p=713.html");