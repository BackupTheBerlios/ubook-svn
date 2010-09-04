<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
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