<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'HttpUrl.php';
require_once 'HttpConnection.php';
require_once 'Message.php';
require_once 'ExternalServer.php';

class ConnectionData {

    private $id = null;
    private $connection = null;
    private $server = null;
    private $newServers = '';

    public function __construct($id, $connection, $server) {
        $this->id = $id;
        $this->connection = $connection;
        $this->server = $server;
    }

    public function read() {
        return $this->connection->read();
    }

    public function end() {
        return $this->connection->end();
    }

    public function getId() {
        return $this->id;
    }

    public function createBookList() {
        try {
            $message = Message::createFromXml($this->connection->getBody());
        } catch (Exception $ex) {
            $this->server->failed();
            return;
        }
        $this->server->setLocationName($message->fromServer());
        $this->newServers = $message->getNewServers();
        return new ExternalBookList($message->fromServer(), $message->bookList());

    }

    public function getNewServers() {
        return $this->newServers;
    }

}

?>