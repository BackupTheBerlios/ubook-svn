<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
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
                . ' and next_try <= curdate()'
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