<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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