<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

include_once 'magic_quotes.php';

class SearchKey {

	private $key = null;

	public function __construct() {
		$this->get();
	}

	public function isGiven() {
		if ($this->key === null) return false;
		else return true;
	}

	public function asText() {
		return $this->key;
	}

	public function asHtml() {
		return text2html(stripslashes($this->key));
	}

	public function getOption() {
		if (isset($_GET['new'])) return 'new';
		if (isset($_GET['random'])) return 'random';
		return false;
	}

	private function get() {
		if (isset($_GET['search'])) {
			$this->key = trim($_GET['search']);
		}
	}

}
?>