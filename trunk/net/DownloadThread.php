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
        $this->join();
        return $this->connection->getBody();
    }

}

?>