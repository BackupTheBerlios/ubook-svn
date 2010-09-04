<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'LocalBookList.php';

require_once 'mysql_conn.php';

class CategoryBookList extends LocalBookList {

    private $category;

    public function __construct($category) {
        $this->category = $category;
    }

    protected function createMysqlQuery() {
        $category = $this->category;
        $q = 'select books.id, books.author, books.title, books.price from books';
        if ($category == 'Sonstiges') {
            $q .= ' left join book_cat_rel
		 on books.id=book_cat_rel.book_id
		 where book_cat_rel.category="' . $category . '"
		 or book_cat_rel.category is null';
        } else {
            $q .= ' join book_cat_rel
		 on books.id=book_cat_rel.book_id
		 where book_cat_rel.category="' . $category . '"';
        }
        $q .= ' order by books.author, books.title, books.price;';
        return $q;
    }

}

?>