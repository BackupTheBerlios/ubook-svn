<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'Parser.php';

/*
 * Fetches a book array from a MySQL result.
 */
class BookFetcher {

	public static function fetch(&$result) {
		$bookArray = mysql_fetch_array($result);
		if (!$bookArray) return false;
		$bookArray['price'] = str_replace('.', ',', $bookArray['price']);
		return $bookArray;
	}

	public static function fetchHtml(&$result) {
		$bookArray = self::fetch($result);
		if (!$bookArray) return false;
		Parser::htmlbook($bookArray);
		return $bookArray;
	}

}
?>
