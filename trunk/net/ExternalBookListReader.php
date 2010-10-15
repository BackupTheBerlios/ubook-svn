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

    private $serverPool;
    private $serverPoolArray;
    private $scriptRequest;
    private $acceptServers;
    private $bookListArray = array();

    public function __construct(ExternalServerPool $externalServerPool, SearchKey $searchKey) {
        if ($searchKey->getOption()) {
            // Options are not supported.
            return;
        }
        $this->serverPool = $externalServerPool;
        $this->serverPoolArray = &$externalServerPool->toArray();
        $this->scriptRequest = self::scriptRequest($searchKey);
        $localServer = new LocalServer();
        $this->acceptServers = $localServer->acceptSuggestedServers();
        if ($this->hasServer(0)) {
            $this->startNextQueries();
        }
    }

    public function readNextGroup($maxDistanceGroup) {
        if ($this->serverPool == null) {
            // see __construct
            return $this->bookListArray;
        }
        if ($maxDistanceGroup == 0) {
            Thread::joinAll();
            return $this->bookListArray;
        }
        while ($this->hasServer($maxDistanceGroup) && !count($this->bookListArray)) {
            $this->startNextQueries();
            Thread::joinAll();
        }
        return $this->bookListArray;
    }

    public function serverSuggested(ExternalServer $server) {
        if ($this->acceptServers) {
            if ($server->isNew()) {
                $this->serverPool->add($server);
            }
            if (current($this->serverPoolArray) == $server) {
                $this->createServerQuery($server);
                next($this->serverPoolArray);
            }
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

    private function hasServer($maxDistanceGroup) {
        $server = current($this->serverPoolArray);
        if (!$server) {
            return false;
        }
        $currentGroup = $server->getDistanceGroup();
        if ($currentGroup > $maxDistanceGroup) {
            return false;
        }
        return true;
    }

    private function startNextQueries() {
        $server = current($this->serverPoolArray);
        $currentGroup = $server->getDistanceGroup();
        do {
            $this->createServerQuery($server);
            $server = next($this->serverPoolArray);
        } while ($server && $server->getDistanceGroup() == $currentGroup);
    }

    private function createServerQuery(ExternalServer $server) {
        $url = new HttpUrl($server->getUrl() . $this->scriptRequest);
        new ServerQuery($server, $url, $this);
    }

}

?>