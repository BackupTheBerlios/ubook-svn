<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'mysql_conn.php';

class Updater {

    private $version = '0';
    private $nextUpdate = null;

    public function __construct() {
        $this->checkVersion();
    }

    public function hasWork() {
        $next = $this->nextUpdate();
        if ($next) {
            $this->nextUpdate = $next;
            return true;
        } else {
            return false;
        }
    }

    public function update() {
        if ($this->nextUpdate) {
            include_once $this->nextUpdate;
        }
    }

    private function checkVersion() {
        $result = mysql_query('select db_version from db_version');
        if (!$result) {
            return;
        }
        $this->version = current(mysql_fetch_row($result));
    }

    private function nextUpdate() {
        $iterator = new DirectoryIterator('update');
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $iterator->next();
            if (strlen($entry) != 21) {
                continue;
            }
            if (substr($entry, 0, 7) != 'update_') {
                continue;
            }
            if (substr($entry, -4) != '.php') {
                continue;
            }
            $version = substr($entry, 7, 10);
            if ($version > $this->version) {
                return $entry;
            }
        }
    }

}

?>