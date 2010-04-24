<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'IsbnDbProvider.php';

require_once 'net/HttpUrl.php';
require_once 'net/ThreadedDownloader.php';

/**
 * Fetches information about a book from the API of isbndb.com.
 * @author maikel
 */
class IsbnDbDotComProvider implements IsbnDbProvider {

    private $book;

    public function urlFor($isbn) {
        $urlString = 'http://isbndb.com/api/books.xml?'
        . 'access_key=FGOZ2S4A&index1=isbn&value1='.$isbn;
        return new HttpUrl($urlString);
    }

    public function process($xmlString) {
		try {
			$xml = @new SimpleXMLElement($xmlString);
		} catch (Exception $ex) {
			// malformed xml
			return;
		}
        if (!isset($xml->BookList)) return;
        if (!isset($xml->BookList->BookData)) return;
        $book = array();
        $xmlBook = $xml->BookList->BookData[0];
        $book['isbn'] = (string) $xmlBook['isbn'];
        $book['isbn13'] = (string) $xmlBook['isbn13'];
        $xmlTitle = $xmlBook->Title;
        $book['title'] = (string) $xmlTitle;
        $xmlAuthor = $xmlBook->AuthorsText;
        $book['author'] = (string) $xmlAuthor;
        $book['year'] = '';
        $this->book = $book;
    }

    public function getBook() {
        return $this->book;
    }

}

?>