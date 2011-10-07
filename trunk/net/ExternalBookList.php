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

require_once 'books/BookList.php';
require_once 'Message.php';

class ExternalBookList implements BookList {

    private $from;
    private $books;

    public function __construct($from, array $bookList) {
        $this->from = $from;
        $this->books = $bookList;
    }

    public function locationName() {
        return $this->from;
    }

    public function size() {
        return sizeof($this->books);
    }

    public function toHtmlRows() {
        $template = new Template('<tr><td><a href="\'url\'" target="_blank">'
                        . '<!-- begin author -->\'author\': <!-- end author -->'
                        . '\'title\'</a></td>'
                        . '<td>\'price\'&nbsp;&euro;</td></tr>' . "\n");
        $html = '';
        foreach ($this->books as $i => $book) {
            $t = clone $template;
            $t->assign('url', $book->getUrl());
            if ($book->getAuthor()) {
                $t->addSubtemplate('author');
                $t->assign('author', $book->getAuthor());
            }
            $t->assign('title', $book->getTitle());
            $t->assign('price', $book->getPrice());
            $html .= $t->result();
        }
        return $html;
    }

}

?>