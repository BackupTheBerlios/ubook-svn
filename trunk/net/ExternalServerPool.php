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

require_once 'ExternalServer.php';

class ExternalServerPool {
    const QUERY_START = 'select url, name, distgroup, fails, next_try from servers ';

    private $servers = array();

    /**
     * Returns servers, which are queried for books.
     * @return ExternalServerPool pool of authorized and answering ones
     */
    public static function activeServerPool() {
        $pool = new self();
        $pool->loadActive();
        return $pool;
    }

    /**
     * Returns all authorized servers, active + temporary not available.
     * @return ExternalServerPool pool of activated ones
     */
    public static function whiteServerPool() {
        $pool = new self();
        $pool->loadWhite();
        return $pool;
    }

    /**
     * Returns all servers without a name.
     * @return ExternalServerPool pool of servers, which have not answered
     * yet
     */
    public static function unknownServerPool() {
        $pool = new self();
        $pool->loadUnknown();
        return $pool;
    }

    /**
     * Returns all blacklisted servers.
     * @return ExternalServerPool pool of explizitly blacklisted servers
     */
    public static function blacklistServerPool() {
        $pool = new self();
        $pool->loadBlacklist();
        return $pool;
    }

    public function add(ExternalServer $newServer) {
        $serverArray = $this->servers;
        foreach ($this->servers as $server) {
            if ($server->equals($newServer)) {
                return;
            }
        }
        $this->servers[] = $newServer;
    }

    public function &toArray() {
        return $this->servers;
    }

    public function size() {
        return sizeof($this->servers);
    }

    private function loadActive() {
        $query = self::QUERY_START
                . ' where url != ""'
                . ' and next_try <= now()'
                . ' order by distgroup;';
        $this->loadFromDb($query);
    }

    private function loadWhite() {
        $query = self::QUERY_START
                . ' where url != ""'
                . ' and next_try < "9999-12-31";';
        $this->loadFromDb($query);
    }

    private function loadUnknown() {
        $query = self::QUERY_START
                . ' where url != ""'
                . ' and name = ""'
                . ' and next_try = "9999-12-31";';
        $this->loadFromDb($query);
    }

    private function loadBlacklist() {
        $query = self::QUERY_START
                . ' where url != ""'
                . ' and name != ""'
                . ' and next_try = "9999-12-31";';
        $this->loadFromDb($query);
    }

    private function loadFromDb($query) {
        $result = mysql_query($query);
        if (!$result)
            return;
        while ($serverArray = mysql_fetch_array($result)) {
            $this->servers[] = ExternalServer::newFromDbArray($serverArray);
        }
    }

}

?>