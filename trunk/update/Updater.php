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

require_once 'mysql_conn.php';

class Updater {
    const PREFIX = 'update_';
    const SUFFIX = '.inc';

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

    public function getNextVersion() {
        return $this->nextUpdate;
    }

    public function update() {
        if ($this->nextUpdate) {
            include_once self::PREFIX . $this->nextUpdate . self::SUFFIX;
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
        $prefixLength = strlen(self::PREFIX);
        $suffixLength = strlen(self::SUFFIX);
        $updateNameLength = $prefixLength + 10 + $suffixLength;
        $iterator = new DirectoryIterator('update');
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $iterator->next();
            if (strlen($entry) != $updateNameLength) {
                continue;
            }
            if (substr($entry, 0, $prefixLength) != self::PREFIX) {
                continue;
            }
            if (substr($entry, -$suffixLength) != self::SUFFIX) {
                continue;
            }
            $version = substr($entry, $prefixLength, 10);
            if ($version > $this->version) {
                return $version;
            }
        }
    }

    private function queryOrFail($query) {
        mysql_query($query) or die('Update failed!'
                        . ' Could not execute MySQL: ' . $query);
    }

    private function updateFinished() {
        mysql_query('update db_version'
                . ' set db_version = "' . $this->nextUpdate . '";');
    }

}

?>