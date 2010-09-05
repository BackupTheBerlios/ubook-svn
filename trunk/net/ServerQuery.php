<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'DownloadThread.php';

/**
 * Queries an external server.
 */
class ServerQuery extends DownloadThread {

    private $server;
    private $reader;

    public function __construct(ExternalServer $server, HttpUrl $url, ExternalBookListReader $reader) {
        parent::__construct($url);
        $this->server = $server;
        $this->reader = $reader;
    }

    public function step() {
        parent::step();
        if (parent::isFinished()) {
            $this->finish();
        }
    }

    public function finish() {
        try {
            $message = Message::createFromXml(parent::getResult());
        } catch (Exception $ex) {
            $this->server->failed();
            return;
        }
        $this->server->setLocationName($message->fromServer());
        foreach ($message->getNewServers() as $s) {
            $this->reader->serverSuggested($s);
        }
        $list = new ExternalBookList($message->fromServer(), $message->bookList());
        if ($list->size()) {
            $this->reader->addBookList($list);
        }
    }

}

?>