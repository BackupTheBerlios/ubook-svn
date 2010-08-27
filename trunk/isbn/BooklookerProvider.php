<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'IsbnDbProvider.php';

require_once 'books/Book.php';
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

    public function urlFor(Isbn $isbn) {
        $urlString = 'http://www.booklooker.de/interface/search.php'
                . '?pid=' . $this->pid . '&medium=book&isbn='.$isbn->toString();
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
        $xmlBook = $xml->Book[0];
        $this->book = new Book(array(
                        'author' => (string) $xmlBook->Author,
                        'title' => (string) $xmlBook->Title,
                        'year' => (string) $xmlBook->Year,
                        'isbn' => (string) $xmlBook->ISBN
        ));
    }
    
    public function getBook() {
        return $this->book;
    }

}

?>