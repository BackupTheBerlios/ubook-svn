<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'HttpUrl.php';

class HttpConnection {
    const blocking = 1;
    const nonBlocking = 0;

    const newline = "\r\n";
    const emptyline = "\r\n\r\n";

    private $url = null;
    private $fp = null;
    private $response = null;
    private $body = null;

    public function __construct($httpUrl) {
        if ($httpUrl == null) {
            throw new Exception('URL must not be null!');
        }
        $this->url = $httpUrl;
    }

    /**
     * Opens a non-blocking socket connection, puts a http request and returns the pointer.
     * @return file-pointer
     */
    public function open($blockingMode=nonBlocking) {
        $request = self::createRequest();
        $filePointer = @fsockopen($this->url->getDomainName(), 80);
        if ($filePointer === false)
            return null;
        stream_set_blocking($filePointer, $blockingMode);
        fputs($filePointer, $request);
        $this->fp = $filePointer;
        $this->response = '';
        return $filePointer;
    }

    /**
     * Reads non-blocking some bytes, if present.
     * @return string or null on end of stream
     */
    public function read() {
        if (!$this->fp)
            return null;
        $someBytes = fread($this->fp, 1024);
        $this->response .= $someBytes;
        if (feof($this->fp)) {
            fclose($this->fp);
            $this->fp = null;
            $this->parseResponse();
        }
        return $someBytes;
    }

    /**
     * Checks wether the stream end is reached.
     * @return bool
     */
    public function end() {
        if (!$this->fp)
            return true;
        return feof($this->fp);
    }

    /**
     * If the stream successfully ended, the message body will be returned.
     * @return string or null on incomplete or failed reading
     */
    public function getBody() {
        return $this->body;
    }

    private function parseResponse() {
        list($header, $body) = explode(self::emptyline, $this->response, 2);
        /*
         * The Status-Code is not used at the moment.
         *
          list($statusLine, $generalHeader) = split(self::newline, $header, 2);
          $statusCode = substr($statusLine, 9, 3);
          $this->statusCode = $statusCode;
         */
        $this->body = $body;
    }

    private function createRequest() {
        $request = 'GET ' . $this->url->getDirectory() . ' HTTP/1.0' . self::newline
                . 'Host: ' . $this->url->getDomainName() . self::newline
                . 'Connection: close' . self::emptyline;
        return $request;
    }

}

?>