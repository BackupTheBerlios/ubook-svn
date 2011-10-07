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