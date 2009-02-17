<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/SearchKey.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) exit;

require_once 'LocalServerName.php';
$serverName = new LocalServerName();
echo '<!-- section -->'."\n";
echo $serverName->name()."\n";

require_once 'books/SearchKeyBookList.php';
$bookList = new SearchKeyBookList($searchKey, true);

echo '<!-- section -->'."\n";
echo $bookList->size()."\n";

echo '<!-- section -->'."\n";
if ($bookList->size() > 0) {
	echo $bookList->toHtmlTable();
}
else {
	require_once 'books/ExternalServer.php';
	require_once 'books/ExternalServerPool.php';
	$serverPool = new ExternalServerPool();
	while ($server = $serverPool->next()) {
		echo $server->toXml()."\n";
	}
}

?>