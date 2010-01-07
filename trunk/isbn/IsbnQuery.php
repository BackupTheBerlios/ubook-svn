<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'net/HttpUrl.php';
require_once 'net/HttpConnection.php';

/**
 * Fetches information about a book from the API of Uni Karlsruhe.
 * @author maikel
 */
abstract class IsbnQuery {

	public static function query($isbn) {
		$urlString = 'http://www.ubka.uni-karlsruhe.de/hylib-bin/suche.cgi'
		. '?opacdb=UBKA_OPAC&simple_search=isbn%3D' . $isbn
		. '&raw=1&einzeltreffer=kurz';
		$url = new HttpUrl($urlString);
		$connection = new HttpConnection($url);
		$connection->open(HttpConnection::blocking);
		$connection->read();
		$bookArray = self::raw2book($connection->getBody());
		$bookArray['isbn'] = $isbn;
		return $bookArray;
	}

	private static function raw2book($rawText) {
		$lines = explode("\n", $rawText);
		if (sizeof($lines) != 4) return array();
		$line = utf8_encode($lines[1]);
		list($number, $titleAuthorYear) = explode(' ', $line, 2);
		list($title, $authorYear) = explode(' / ', $titleAuthorYear);
        list($author, $year) = explode(' , ', $authorYear);
        return array(
        'author' => $author,
        'title' => $title,
        'year' => $year
        );
	}

}

?>