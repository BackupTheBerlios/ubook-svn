<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2019 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'net/ThreadedDownloader.php';

class ThreadedDownloaderTest extends PHPUnit_Framework_TestCase {

    const TESTURL = 'http://localhost/';
    const AMOUNT = 50;

    private $testUrl;

    function __construct() {
        $this->testUrl = new HttpUrl(self::TESTURL);
    }

    function testStartDownload() {
        $future = ThreadedDownloader::startDownload($this->testUrl);
        $this->assertNotNull($future);
        ThreadedDownloader::finishAll();
        $result = $future->getContent();
        $this->assertNotNull($result);
    }

    function testStartMultipleDownload() {
        $futures = array();
        for ($i = 0; $i < self::AMOUNT; $i++) {
            $f = ThreadedDownloader::startDownload($this->testUrl);
            $this->assertNotNull($f);
            $futures[] = $f;
        }
        ThreadedDownloader::finishAll();
        foreach ($futures as $i => $f) {
            $result = $f->getContent();
            $this->assertNotNull($result);
        }
    }

    function testStartDownloadWithProcessor() {
        $processor = new TestProceccor();
        $future = ThreadedDownloader::startDownload($this->testUrl, $processor);
        ThreadedDownloader::finishAll();
        $this->assertNotNull($processor->getData());
    }

}

class TestProceccor implements Processor {

    private $data = null;

    public function process($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }
}
?>