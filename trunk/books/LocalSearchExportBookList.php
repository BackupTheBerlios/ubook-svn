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