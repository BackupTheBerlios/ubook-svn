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
 * Fetches information about a book from the API of Uni Karlsruhe.
 * @author maikel
 */
class UBKarlsruheProvider implements IsbnDbProvider {

    private $isbn;
    private $book;

    public function urlFor($isbn) {
        $this->isbn = $isbn;
        $urlString = 'http://www.ubka.uni-karlsruhe.de/hylib-bin/suche.cgi'
                . '?opacdb=UBKA_OPAC&simple_search=isbn%3D' . $isbn
                . '&raw=1&einzeltreffer=kurz';
        return new HttpUrl($urlString);
    }

    public function process($rawText) {
        $lines = explode("\n", $rawText);
        if (sizeof($lines) != 4) return array();
        $line = utf8_encode($lines[1]);
        list($number, $titleAuthorYear) = explode(' ', $line, 2);
        list($title, $authorYear) = explode(' / ', $titleAuthorYear);
        list($author, $year) = explode(' , ', $authorYear);
        $this->book = new Book($author, $title, $year, '', $this->isbn);
    }

    public function getBook() {
        return $this->book;
    }

}

?>