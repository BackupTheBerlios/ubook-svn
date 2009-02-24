<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/SearchKey.php';
require_once 'net/Message.php';
require_once 'net/LocalServer.php';
require_once 'books/SearchKeyExportBookList.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) exit;

$localServer = new LocalServer();

if (isset($_GET['from']) && $localServer->rememberNewServers()) {
	require_once 'net/ExternalServer.php';
	$requestingServer = ExternalServer::newFromUrlString($_GET['from']);
	if ($requestingServer) {
		require_once 'mysql_conn.php';
		$query = 'insert into servers (url, next_try) values ('
		. '"'.$requestingServer->getUrl().'", '
		. '"9999-12-31");';
		mysql_query($query);
	}
}

$bookList = new SearchKeyExportBookList($searchKey);

$bookListString = '';
$serverTextList = '';

if ($bookList->size() > 0) {
	$bookListString = $bookList->toTextList();
}
else {
	require_once 'net/ExternalServerPool.php';
	$serverPool = new ExternalServerPool();
	$serverTextList = $serverPool->toTextList(); 
}

$message = new Message($localServer->name(), $bookList->size(), $bookListString, $serverTextList);
echo $message->toString();

?>