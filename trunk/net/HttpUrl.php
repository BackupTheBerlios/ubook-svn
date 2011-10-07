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

class HttpUrl {

	private $urlString = '';
	private $domainName = '';
	private $directory = '';

	public function __construct($urlString) {
		$this->parseUrl($urlString);
	}

	public function toString() {
		return $this->urlString;
	}

	public function getDomainName() {
		return $this->domainName;
	}

	public function getDirectory() {
		return $this->directory;
	}
	
	private function parseUrl($serverUrl) {
		$protocol = 'http://';
		if (!self::stringHasPrefix($serverUrl, $protocol)) return;
		$serverAndDirectory = substr($serverUrl, strlen($protocol));
		$directory = strstr($serverAndDirectory, '/');
		$serverName = substr($serverAndDirectory, 0, -(strlen($directory)));
		$this->domainName = $serverName;
		$this->directory = $directory;
	}

	private static function stringHasPrefix($string, $prefix) {
		if (substr($string, 0, strlen($prefix)) == $prefix) {
			return true;
		}
		return false;
	}

}

?>