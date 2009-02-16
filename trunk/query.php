<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/SearchKey.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) exit;

require_once 'books/SearchKeyBookList.php';
$bookList = new SearchKeyBookList($searchKey, true);

echo $bookList->size()."\n";
echo $bookList->toHtmlTable();

if ($bookList->size() == 0) {
	include 'external_servers.php';
	foreach ($external_servers as $i => $server) {
		echo $server->toXml()."\n";
	}
}

?>