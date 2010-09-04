<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'Isbn.php';
require_once 'Providers.php';

require_once 'books/Book.php';

/**
 * Fetches information about a book from different APIs.
 * @author maikel
 */
class IsbnQuery {

    public static function query(Isbn $isbn) {
        $providers = Providers::createProviders();
        foreach ($providers as $i => $p) {
            $p->provideBookFor($isbn);
        }
        foreach ($providers as $i => $p) {
            $book = $p->getBook();
            if ($book) {
                return $book;
            }
        }
        return new Book(array(
            'isbn' => $isbn->toString()
        ));
    }

}

?>