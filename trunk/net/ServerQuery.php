<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
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