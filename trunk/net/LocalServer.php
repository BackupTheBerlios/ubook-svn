<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';

class LocalServer {

	private $name = '';
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

	public function update($newName) {
		$newName = trim($newName);
		if (!$newName) return;
		if ($this->isEmpty()) {
			$query = 'insert into servers (url, name) values ("", '
			. '"' . $newName . '");';
		}
		else {
			$query = 'update servers set '
			. 'name = "' . $newName . '" '
			. 'where url = "";';
		}
		mysql_query($query);
	}
	
	public function rememberNewServers() {
		return ($this->trustLevel == 0);
	}

	public function acceptSuggestedServers() {
		return ($this->trustLevel == 0);
	}

}
?>
