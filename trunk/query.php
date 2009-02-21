<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/SearchKey.php';
require_once 'books/Message.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) exit;

if (isset($_GET['from'])) {
	require_once 'books/ExternalServer.php';
	$requestingServer = ExternalServer::newFromUrlString($_GET['from']);
	if ($requestingServer) {
		$requestingServer->dbInsert();
	}
}

require_once 'LocalServerName.php';
$serverName = new LocalServerName();

require_once 'books/SearchKeyExportBookList.php';
$bookList = new SearchKeyExportBookList($searchKey);

$bookListString = '';
$serverTextList = '';

if ($bookList->size() > 0) {
	$bookListString = $bookList->toTextList();
}
else {
	require_once 'books/ExternalServerPool.php';
	$serverPool = new ExternalServerPool();
	$serverTextList = $serverPool->toTextList(); 
}

$message = new Message($serverName->name(), $bookList->size(), $bookListString, $serverTextList);
echo $message->toString();

?>