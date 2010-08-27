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
 * Fetches information about a book from the API of Google Books.
 * @author maikel
 */
class GoogleProvider implements IsbnDbProvider {

    private $isbn;
    private $book;

    public function urlFor(Isbn $isbn) {
        $this->isbn = $isbn;
        $urlString = 'http://books.google.com/books/feeds/volumes'
                . '?q=isbn%3A' . $isbn->toString();
        return new HttpUrl($urlString);
    }

    public function process($atomPub) {
        try {
            $xml = @new SimpleXMLElement($atomPub);
        } catch (Exception $ex) {
            // malformed xml
            return;
        }
        if (!isset($xml->entry)) return;
        $xmlBook = $xml->entry[0];
        $bookArray = array();
        $dcChilds = $xmlBook->children('dc', true);
        $authors = array();
        foreach ($dcChilds->creator as $a) {
        	$names = explode(' ', $a);
        	$last = end($names);
        	unset($names[sizeof($names)-1]);
        	$authors[] = $last . ', ' . implode(' ', $names);
        }
        $year = substr($dcChilds->date, 0, 4);
        $this->book = new Book(array(
                        'author' => implode(' and ', $authors),
                        'title' => (string) $dcChilds->title,
                        'year' => $year,
                        'isbn' => $this->isbn->toString()
        ));
    }

    public function getBook() {
        return $this->book;
    }

}

?>
