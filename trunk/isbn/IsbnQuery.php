<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'Isbn.php';
require_once 'IsbnDbProvider.php';
require_once 'LocalIsbnProvider.php';
require_once 'HttpProviders.php';

require_once 'books/Book.php';

/**
 * Fetches information about a book from different APIs.
 * @author maikel
 */
class IsbnQuery implements IsbnDbProvider {

    /**
     * {@inheritdoc }
     * @param Isbn $isbn to search for
     * @return Book containing found data
     */
    public static function query(Isbn $isbn) {
        $localProvider = new LocalIsbnProvider();
        $book = $localProvider->query($isbn);
        if ($book) {
            return $book;
        }
        $providers = HttpProviders::createProviders();
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