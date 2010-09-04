<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'LocalSearchBookList.php';

require_once 'mysql_conn.php';
require_once 'books/Book.php';
require_once 'tools/WEBDIR.php';

class LocalSearchExportBookList extends LocalSearchBookList {

    public function __construct($searchKey, $absoluteUrl = false) {
        parent::__construct($searchKey);
    }

    public function getList() {
        $mysqlResult = parent::getMysqlResult();
        $bookScriptUrl = WEBDIR . 'book.php';
        $list = array();
        while ($book = Book::fromMySql($mysqlResult)) {
            $url = $bookScriptUrl . '?id=' . $book->get('id');
            $extBook = new ExternalBook($url, $book->get('author'),
                            $book->get('title'), $book->get('price'));
            $list[] = $extBook;
        }
        return $list;
    }

}

?>