<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'Isbn.php';
require_once 'IsbnDbProvider.php';

require_once 'books/Book.php';

/**
 * Fetches information about a book from the local database.
 * @author maikel
 */
class LocalIsbnProvider implements IsbnDbProvider {

    public static function query(Isbn $isbn) {
        include_once 'mysql_conn.php';
        $fields = array('author', 'title', 'price', 'year', 'isbn',
            'description');
        $query = 'select ' . implode(', ', $fields)
                . ' from books where isbn="' . $isbn->toString() . '"'
                . ' order by price;';
        $result = mysql_query($query);
        $book = Book::fromMySql($result);
        if (!$book) {
            return null;
        }
        while ($b = Book::fromMySql($result)) {
            $isComplete = true;
            foreach ($fields as $field) {
                if ($book->get($field)) {
                    continue;
                }
                if ($b->get($field)) {
                    $book->set($field, $b->get($field));
                    continue;
                }
                $isComplete = false;
            }
            if ($isComplete) {
                break;
            }
        }
        return $book;
    }

}

?>