<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

/*
 * A collection of parsing functions
 */
class Parser {

	/*
	 * Convert all applicable characters to HTML entities.
	 * The charset parameter was added in PHP version 4.1.0.
	 * static public
	 */
	function text2html($text) {
		$quoteStyle = ENT_QUOTES;
		$charset = 'UTF-8';
		$html = htmlentities($text, $quoteStyle, $charset);
		return $html;
	}

	function htmlbook(&$book) {
		$book['author'] = self::text2html($book['author']);
		$book['title'] = self::text2html($book['title']);
		$book['description'] = self::text2html($book['description']);
		return $book;
	}

}
?>
