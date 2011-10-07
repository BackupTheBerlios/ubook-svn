<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
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

include_once 'magic_quotes.php';
require_once 'tools/Parser.php';

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
		return Parser::text2html(stripslashes($this->key));
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