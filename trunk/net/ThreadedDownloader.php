<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'HttpConnection.php';
require_once 'HttpUrl.php';

require_once 'tools/CircleList.php';

/**
 * Downloads multiple websites in non-blocking mode.
 */
class ThreadedDownloader {

    private static $downloads = null;

    public static function startDownload(HttpUrl $httpUrl, Processor $processor = null) {
        if ($httpUrl == null) {
            throw new Exception("URL must no be null!");
        }
        if (self::$downloads == null) {
            self::$downloads = new CircleList();
        }
        $conn = new HttpConnection($httpUrl);
        $conn->open();
        $data = new DownloadData($conn);
        if ($processor) {
            $data->setProcessor($processor);
        }
        self::$downloads->add($data);
        return $data->getFuture();
    }

    public static function finishAll() {
        while ($d = self::$downloads->getNext()) {
            $d->read();
            if ($d->isFinished()) {
                self::$downloads->deleteActual();
            }
        }
    }

}

class FutureDownload {

    private $content = null;

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

}

interface Processor {

    function process($data);

}

class DownloadData {

    private $conn;
    private $future;
    private $finished = false;
    private $processor;

    public function  __construct(HttpConnection $conn) {
        $this->conn = $conn;
        $this->future = new FutureDownload();
        $this->nextData = $this;
        $this->prevData = $this;
    }

    public function setProcessor(Processor $processor) {
        $this->processor = $processor;
    }

    public function getFuture() {
        return $this->future;
    }

    public function read() {
        $this->conn->read();
        if ($this->conn->end()) {
            $content = $this->conn->getBody();
            $this->future->setContent($content);
            if ($this->processor != null) {
                $this->processor->process($content);
            }
            $this->finished = true;
        }
    }

    public function isFinished() {
        return $this->finished;
    }

}

?>