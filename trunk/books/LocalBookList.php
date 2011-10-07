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

require_once 'BookList.php';

require_once 'mysql_conn.php';
require_once 'books/Book.php';

abstract class LocalBookList implements BookList {

    private static $numberOfAllBooks = null;

    public static function numberOfAllBooks() {
        if (self::$numberOfAllBooks === null) {
            $countResult = mysql_query('select count(id) from books;');
            list($null, self::$numberOfAllBooks) = each(mysql_fetch_row($countResult));
        }
        return self::$numberOfAllBooks;
    }

    private $result = null;

    public function size() {
        return mysql_num_rows($this->getMysqlResult());
    }

    public function toHtmlRows() {
        $template = new Template('<tr><td><a href="book.php?id=\'id\'">'
                        . '<!-- begin author -->\'author\': <!-- end author -->'
                        . '\'title\'</a></td>'
                        . '<td>\'price\'&nbsp;&euro;</td></tr>' . "\n");
        $html = '';
        while ($book = Book::fromMySql($this->getMysqlResult())) {
            $t = clone $template;
            $book->assignHtmlToTemplate($t);
            if ($book->get('author')) {
                $t->addSubtemplate('author');
            }
            $html .= $t->result();
        }
        return $html;
    }

    protected function getMysqlResult() {
        if ($this->result === null) {
            $this->result = mysql_query($this->createMysqlQuery());
        }
        return $this->result;
    }

    protected abstract function createMysqlQuery();
}

?>