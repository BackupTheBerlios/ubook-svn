<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

class BookFormatter {

	public static function asText($bookArray) {
		$text = ' Autor: ' . $bookArray['author'] . "\n"
		 . ' Titel: ' . $bookArray['title'] . "\n"
		 . ' Preis: ' . $bookArray['price'] . ' Euro' . "\n"
		 . ' Erscheinungsjahr: ' . $bookArray['year'] . "\n"
		 . ' Beschreibung:' . "\n"
		 . $bookArray['description'];
		return $text;
	}

}

?>
