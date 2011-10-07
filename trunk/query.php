<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'books/SearchKey.php';
require_once 'net/Message.php';
require_once 'net/LocalServer.php';
require_once 'books/LocalSearchExportBookList.php';

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

$bookList = new LocalSearchExportBookList($searchKey);

$message = new Message($localServer->name());
if ($bookList->size() > 0) {
	$message->setBooks($bookList->getList());
}
else {
	require_once 'net/ExternalServerPool.php';
	$serverPool = ExternalServerPool::activeServerPool();
	$message->setServers($serverPool->toArray());
}

header('Content-Type: text/xml; charset=utf-8');
echo $message->toXmlString();

?>