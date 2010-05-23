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
 * Fetches information about a book from the API of isbndb.com.
 * @author maikel
 */
class IsbnDbDotComProvider implements IsbnDbProvider {

    private $accessKey;
    private $book;

    public function  __construct($accessKey) {
        $this->accessKey = $accessKey;
    }

    public function urlFor($isbn) {
        $urlString = 'http://isbndb.com/api/books.xml?'
                . 'access_key=' . $this->accessKey
                . '&index1=isbn&value1=' . $isbn;
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
        $xmlBook = $xml->BookList->BookData[0];
        $this->book = new Book(array(
                        'author' => (string) $xmlBook->AuthorsText,
                        'title' => (string) $xmlBook->Title,
                        'isbn' => (string) $xmlBook['isbn']
        ));
    }

    public function getBook() {
        return $this->book;
    }

}

?>