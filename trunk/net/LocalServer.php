<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';

class LocalServer {

	private $name = '';
	/*
	 * The trust level encodes two options:
	 * 1. Remember unknown servers,
	 * 2. Add servers suggested from others.
	 * These levels are possible:
	 * 0: manual configuration
	 * 1: remember unknown
	 * 2: add suggested
	 * 3: do both
	 */
	private $trustLevel = 0;

	public function __construct() {
		$q = 'select name, fails as trust_level from servers where url="";';
		$r = mysql_query($q);
		if (!$r) return;
		if ($arr = mysql_fetch_array($r)) {
			$this->name = $arr['name'];
			$this->trustLevel = $arr['trust_level'];
		}
	}

	public function isEmpty() {
		if ($this->name) {
			return false;
		}
		else return true;
	}

	public function name() {
		return $this->name;
	}
	
	public function setUp($name) {
		$new_table = 'CREATE TABLE `servers` (';
		$new_table .= '`url` varchar(128) NOT NULL,';
		$new_table .= '`name` varchar(128) NOT NULL,';
		$new_table .= '`distgroup` tinyint unsigned NOT NULL DEFAULT 1,';
		$new_table .= '`fails` tinyint(3) unsigned NOT NULL,';
		$new_table .= '`next_try` datetime NOT NULL,';
		$new_table .= 'PRIMARY KEY  (`url`)';
		$new_table .= ')';
		mysql_query($new_table);
		$this->insert($name);
	}

	private function insert($newName) {
		$newName = trim($newName);
		if (!$newName) return;
		$query = 'insert into servers (url, name, fails) values ("", '
		. '"' . $newName . '", 2);';
		mysql_query($query);
	}

	public function update($newName) {
		$newName = trim($newName);
		if (!$newName) return;
		if ($this->isEmpty()) {
			$query = 'insert into servers (url, name, fails) values ("", '
			. '"' . $newName . '", 2);';
		}
		else {
			$query = 'update servers set '
			. 'name = "' . $newName . '" '
			. 'where url = "";';
		}
		mysql_query($query);
	}

	public function rememberNewServers() {
		return ($this->trustLevel & 1);
	}

	public function acceptSuggestedServers() {
		return ($this->trustLevel & 2);
	}

	public function setRemembering($doRemember) {
		if ($doRemember) {
			$this->trustLevel |= 1;
		}
		else {
			$this->trustLevel &= 2;
		}
		$this->updateLevel();
	}

	public function setAccepting($doAccept) {
		if ($doAccept) {
			$this->trustLevel |= 2;
		}
		else {
			$this->trustLevel &= 1;
		}
		$this->updateLevel();
	}

	private function updateLevel() {
		$query = 'update servers set'
		. ' fails = ' . $this->trustLevel
		. ' where url = "";';
		mysql_query($query);
	}

}
?>
