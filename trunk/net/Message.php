<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'net/ExternalServer.php';

class Message {

    private $from = '';
    private $bookList = array();
    private $servers = array();

    public static function createFromXml($xmlString) {
        $doc = @DOMDocument::loadXML($xmlString);
        if ($doc == null) {
            throw new Exception('Unvalid XML string.');
        }
    	/*
         * Validating every message agains a definition in the web is wasting
         * time. So let's validate against a local schema.
        if (!$doc->validate()) {
            throw new Exception('Unvalid XML.');
        }
         */
        /*
         * The validation makes a lot of checks for us. We don't have to look
         * for '<' and '>' characters.
         */
        if (!$doc->schemaValidate('uBookMessage.xsd')) {
            throw new Exception('Unvalid uBookMessage.');
        }
        $elem = $doc->documentElement;
        $from = $elem->getAttribute('from');
        $message = new self($from);
        $message->parseBookList($elem);
        $message->parseServerList($elem);
        return $message;
    }

    public function __construct($from) {
        $this->from = $from;
    }

    public function setBooks($books) {
        $this->bookList = $books;
    }

    public function setServers($servers) {
        $this->servers = $servers;
    }

    public function toXmlString() {
        $impl = new DOMImplementation();
        $docTypeName = 'uBookMessage';
        $docTypePublic = '-//uBook/DTD uBookMessage 1//EN';
        $docTypeId = WEBDIR . 'uBookMessage.dtd';
        $docType = $impl->createDocumentType($docTypeName, $docTypePublic, $docTypeId);
        $doc = $impl->createDocument('', '', $docType);
        $doc->encoding = 'UTF-8';
        $doc->xmlStandalone = false;
        $message = $doc->createElement('uBookMessage');
        $message->setAttribute('version', '1');
        $message->setAttribute('from', $this->from);
        $doc->appendChild($message);
        foreach ($this->bookList as $i => $b) {
            $book = $doc->createElement('book');
            $book->setAttribute('url', $b->getUrl());
            $book->setAttribute('author', $b->getAuthor());
            $book->setAttribute('title', $b->getTitle());
            $book->setAttribute('price', $b->getPrice());
            $message->appendChild($book);
        }
        foreach ($this->servers as $i => $s) {
            $server = $doc->createElement('server');
            $server->setAttribute('url', $s->getUrl());
            $message->appendChild($server);
        }
        $doc->formatOutput = true;
        return $doc->saveXML();
    }

    public function fromServer() {
        return $this->from;
    }

    public function resultSize() {
        return sizeof($this->bookList);
    }

    public function bookList() {
        return $this->bookList;
    }

    public function getNewServers() {
        return $this->servers;
    }

    private function parseBookList(DomElement $elem) {
        foreach ($elem->getElementsByTagName('book') as $b) {
            $url = $b->getAttribute('url');
            $author = $b->getAttribute('author');
            $title = $b->getAttribute('title');
            $price = $b->getAttribute('price');
            $this->bookList[] = new ExternalBook($url, $author, $title, $price);
        }
    }

    private function parseServerList(DomElement $elem) {
        foreach ($elem->getElementsByTagName('server') as $s) {
            $url = $s->getAttribute('url');
            $server = ExternalServer::newFromUrlString($url);
            if ($server == null) return;
            $this->servers[] = $server;
        }
    }

}

class ExternalBook {

    private $fields;

    public function  __construct($url, $author, $title, $price) {
        $this->fields = array(
                'url' => $url,
                'author' => $author,
                'title' => $title,
                'price' => $price
        );
    }

    public function getUrl() {
        return $this->fields['url'];
    }

    public function getAuthor() {
        return $this->fields['author'];
    }

    public function getTitle() {
        return $this->fields['title'];
    }

    public function getPrice() {
        return $this->fields['price'];
    }

}

?>