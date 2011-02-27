<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'Isbn.php';
require_once 'HttpIsbnDbProvider.php';

require_once 'books/Book.php';
require_once 'net/HttpUrl.php';

/**
 * Fetches information about a book from the API of isbndb.com.
 * @author maikel
 */
class IsbnDbDotComProvider extends HttpIsbnDbProvider {

    private $accessKey;

    public function  __construct($accessKey) {
        $this->accessKey = $accessKey;
    }

    protected  function urlFor(Isbn $isbn) {
        $urlString = 'http://isbndb.com/api/books.xml?'
                . 'access_key=' . $this->accessKey
                . '&index1=isbn&value1=' . $isbn->toString();
        return new HttpUrl($urlString);
    }

    protected function bookFor($xmlString) {
        try {
            $xml = @new SimpleXMLElement($xmlString);
        } catch (Exception $ex) {
            // malformed xml
            return;
        }
        if (!isset($xml->BookList)) return;
        if (!isset($xml->BookList->BookData)) return;
        $xmlBook = $xml->BookList->BookData[0];
        return new Book(array(
                        'author' => (string) $xmlBook->AuthorsText,
                        'title' => (string) $xmlBook->Title,
                        'isbn' => (string) $xmlBook['isbn']
        ));
    }

}

?>