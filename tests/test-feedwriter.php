<?php

include 'bootstrap.php';

$writer = new FeedWriter();
$writer->language = 'pt_BR';
$writer->title = 'Test';
$writer->description = 'Test';
$writer->image = array(
  'uri' => 'http://www.infoescola.com/wp-content/uploads/2009/12/rss.gif',
  'title' => 'RSS'
);
$writer->dateModified = time();
$writer->lastBuildDate = time();
$writer->link = 'http://facool.com.br';
$writer->setFeedLink('http://facool.com.br/rss', 'atom');
$writer->addAuthor('Facool', 'facool@facool.com.br', 'http://facool.com.br');

$entry = $writer->createEntry();
$entry->title = 'Entry 1';
$entry->description = 'Entry 1 Desc';
$entry->content = 'Entry 1 Content';
$entry->link = 'http://www.facool.com.br/link/1';
$entry->dateModified = time();

$entry = $writer->createEntry();
$entry->title = 'Entry 2';
$entry->description = 'Entry 2 Desc';
$entry->content = 'Entry 2 Content';
$entry->link = 'http://www.facool.com.br/link/2';
$entry->dateModified = time();

echo '<pre>';
echo htmlentities($writer->render('atom')->saveXml());
echo "\n\n";
var_dump($writer);
echo '</pre>';