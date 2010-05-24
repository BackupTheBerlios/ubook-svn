<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
*/

require_once 'BookList.php';

require_once 'mysql_conn.php';
require_once 'books/Book.php';

abstract class AbstractBookList implements BookList {

    private static $numberOfAllBooks = null;

    private $numberOfRows;
    private $booksAsHtmlRows;

    public function size() {
        return $this->numberOfRows;
    }

    public function toHtmlRows() {
        return $this->booksAsHtmlRows;
    }

    protected function setSize($size) {
        $this->numberOfRows = $size;
    }

    protected function setHtmlRows($htmlRows) {
        $this->booksAsHtmlRows = $htmlRows;
    }

    protected static function mysqlResultToHtml($mysqlResult) {
        $template = new Template('<tr><td><a href="book.php?id=\'id\'">'
                        . '<!-- begin author -->\'author\': <!-- end author -->'
                        . '\'title\'</a></td>'
                        . '<td>\'price\'&nbsp;&euro;</td></tr>'."\n");
        $html = '';
        while ($book = Book::fromMySql($mysqlResult)) {
            $t = clone $template;
            $book->assignHtmlToTemplate($t);
            if ($book->get('author')) {
                $t->addSubtemplate('author');
            }
            $html .= $t->result();
        }
        return $html;
    }

    public static function numberOfAllBooks() {
        if (self::$numberOfAllBooks === null) {
            $countResult = mysql_query('select count(id) from books;');
            list($null, self::$numberOfAllBooks) = each(mysql_fetch_row($countResult));
        }
        return self::$numberOfAllBooks;
    }

}

?>