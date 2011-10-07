<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
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