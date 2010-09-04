<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'LocalBookList.php';

class OfferorsBookList extends LocalBookList {

    private $bookId;

    public function __construct($bookId) {
        $this->bookId = $bookId;
    }

    /**
     * Generates a MySQL select statement
     *
     * @return MySQL select statement
     */
    protected function createMysqlQuery() {
        $query = 'select books.id, books.author, books.title, books.price'
                . ' from books books join books author_book on author_book.id = '
                . $this->bookId
                . ' and books.mail = author_book.mail'
                . ' order by author, title, books.price';
        return $query;
    }

}

?>