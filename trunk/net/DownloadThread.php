<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'HttpConnection.php';
require_once 'HttpUrl.php';

require_once 'concurrent/Thread.php';

/**
 * Downloads a website concurrent to other threads.
 */
class DownloadThread extends Thread {

    private $connection;

    public function __construct(HttpUrl $url) {
        parent::__construct();
        $this->connection = new HttpConnection($url);
        $this->connection->open(HttpConnection::nonBlocking);
    }

    public function step() {
        $this->connection->read();
    }

    public function isFinished() {
        return $this->connection->end();
    }

    public function getResult() {
        assert('$this->isFinished()');
        return $this->connection->getBody();
    }

}

?>