<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/WEBDIR.php';
require_once 'HttpUrl.php';
require_once 'mysql_conn.php';

class ExternalServer {

    private $url;
    private $locationName;
    private $distanceGroup = 255;
    private $fails = 0;
    private $nextTry = '0000-01-01';
    private $dataFromDatabase = false;

    public static function newFromDbArray($array) {
        $server = new ExternalServer($array['name'], $array['url']);
        $server->distanceGroup = (int) $array['distgroup'];
        $server->fails = $array['fails'];
        $server->nextTry = $array['next_try'];
        $server->dataFromDatabase = true;
        return $server;
    }

    public static function newFromUrlString($urlString) {
        if (strlen($urlString) <= 7)
            return;
        if (self::containsSpecialChar($urlString))
            return;
        if ($urlString == WEBDIR)
            return;
        $url = new HttpUrl($urlString);
        if ($url->getDomainName() == 'localhost')
            return;
        return new self('', $urlString);
    }

    public static function changeGroup($url, $group) {
        mysql_query('update servers set distgroup="' . $group
                . '" where url="' . $url . '";');
    }

    public static function blacklist($url) {
        mysql_query('update servers set name="' . $url . '" where url="' . $url . '" and name="";');
        mysql_query('update servers set next_try="9999-12-31" where url="' . $url . '";');
    }

    public static function activate($url) {
        mysql_query('update servers set next_try=curdate() where url="' . $url . '";');
    }

    public static function delete($url) {
        mysql_query('delete from servers where url="' . $url . '";');
    }

    public function __construct($locationName, $url) {
        $this->locationName = $locationName;
        $this->url = $url;
    }

    public function getLocationName() {
        return $this->locationName;
    }

    public function setLocationName($name) {
        if (self::containsSpecialChar($name))
            return;
        if ($name == $this->locationName)
            return;
        $this->locationName = $name;
        if ($this->dataFromDatabase) {
            $query = 'update servers set'
                    . ' name = "' . $this->locationName . '"'
                    . ' , fails = 0'
                    . ' where url = "' . $this->url . '";';
            mysql_query($query);
        } else {
            $this->dbInsert();
        }
    }

    public function getUrl() {
        return $this->url;
    }

    public function getName() {
        if ($this->locationName) {
            return $this->locationName;
        } else {
            return $this->url;
        }
    }

    public function getDistanceGroup() {
        return $this->distanceGroup;
    }

    public function equals($otherServer) {
        if ($this->url == $otherServer->url) {
            return true;
        }
        if ($otherServer->locationName) {
            if ($this->locationName == $otherServer->locationName) {
                return true;
            }
        }
        return false;
    }

    public function dbInsert() {
        if ($this->fails) {
            return;
        }
        $query = 'insert into servers (url, name, distgroup) select '
                . '"' . $this->url . '", '
                . '"' . $this->locationName . '", '
                . 'max(distgroup) from servers;';
        mysql_query($query);
        $this->dataFromDatabase = true;
    }

    private function dbSelect() {
        $query = 'select name, url, fails, next_try from servers'
                . ' where url = "' . $this->url . '";';
        $result = mysql_query($query);
        if ($array = mysql_fetch_array($result)) {
            $this->locationName = $array['name'];
            $this->fails = $array['fails'];
            $this->nextTry = $array['next_try'];
            $this->dataFromDatabase = true;
        }
    }

    public function isValid() {
        if ($this->nextTry == '9999-12-31') {
            return false;
        } else {
            return true;
        }
    }

    public function isBlacklisted() {
        if ($this->isValid() == false) {
            return true;
        }
        return false;
    }

    public function isNew() {
        $this->dbSelect();
        if ($this->dataFromDatabase) {
            return false;
        } else {
            return true;
        }
    }

    public function failed() {
        $this->fails++;
        if ($this->dataFromDatabase) {
            if ($this->locationName == '') {
                self::delete(($this->url));
                return;
            }
            if ($this->fails > 8) {
                self::delete($this->url);
                return;
            }
            $query = 'update servers set'
                    . ' fails = fails + 1'
                    . ' , next_try = adddate(curdate(), fails * fails)'
                    . ' where url = "' . $this->url . '";';
            mysql_query($query);
        }
    }

    private static function containsSpecialChar($string) {
        if (strpos($string, '"') !== false)
            return true;
        if (strpos($string, "'") !== false)
            return true;
        if (strpos($string, '\\') !== false)
            return true;
        if (strpos($string, "\0") !== false)
            return true;
        return false;
    }

}

?>