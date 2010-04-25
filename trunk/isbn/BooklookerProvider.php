<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'IsbnDbProvider.php';

require_once 'net/HttpUrl.php';
require_once 'net/ThreadedDownloader.php';

/**
 * Fetches information about a book from the API of booklooker.de.
 * @author maikel
 */
class BooklookerProvider implements IsbnDbProvider {

    private $pid;
    private $book;

    public function  __construct($pid) {
        $this->pid = $pid;
    }

    public function urlFor($isbn) {
        $urlString = 'http://www.booklooker.de/interface/search.php'
        . '?pid=' . $this->pid . '&medium=book&isbn='.$isbn;
        return new HttpUrl($urlString);
    }

    public function process($xmlString) {
		try {
			$xml = @new SimpleXMLElement($xmlString);
		} catch (Exception $ex) {
			// malformed xml
			return;
		}
        if (!isset($xml->Book)) return;
        $book = array();
        $xmlBook = $xml->Book[0];
        $book['isbn'] = (string) $xmlBook->ISBN;
        $book['title'] = (string) $xmlBook->Title;
        $book['author'] = (string) $xmlBook->Author;
        $book['year'] = (string) $xmlBook->Year;
        $this->book = $book;
    }

    public function getBook() {
        return $this->book;
    }

}

?>