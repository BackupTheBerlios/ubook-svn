<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'ExternalBookList.php';
require_once 'ExternalServer.php';
require_once 'ExternalServerPool.php';
require_once 'HttpUrl.php';
require_once 'LocalServer.php';
require_once 'ServerQuery.php';

require_once 'books/SearchKey.php';
require_once 'concurrent/Thread.php';
require_once 'tools/WEBDIR.php';

class ExternalBookListReader {

    private $scriptRequest = null;
    private $acceptServers = true;
    private $bookListArray = array();

    public function __construct(ExternalServerPool $externalServerPool, SearchKey $searchKey) {
        $this->serverPool = $externalServerPool;
        $this->scriptRequest = self::scriptRequest($searchKey);
        foreach ($externalServerPool->toArray() as $server) {
            $this->queryServer($server);
        }
        $localServer = new LocalServer();
        $this->acceptServers = $localServer->acceptSuggestedServers();

    }

    public function read() {
        Thread::joinAll();
        return $this->bookListArray;
        $bookListArray = array();
    }

    public function queryServer(ExternalServer $server) {
        if ($this->acceptServers) {
            $url = new HttpUrl($server->getUrl() . $this->scriptRequest);
            new ServerQuery($server, $url, $this);
        }
    }

    public function addBookList(ExternalBookList $list) {
        $this->bookListArray[] = $list;
    }

    private static function scriptRequest($searchKey) {
        $requestUrlString = 'query.php?search='
                . urlencode($searchKey->asText())
                . '&from=' . WEBDIR;
        return $requestUrlString;
    }

}

?>