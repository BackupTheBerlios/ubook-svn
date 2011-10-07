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

include_once 'magic_quotes.php';
require_once 'books/BookQuery.php';
require_once 'books/Book.php';
require_once 'tools/Parser.php';
require_once 'tools/RssChannel.php';
require_once 'tools/WEBDIR.php';

function create_rss($search, $limit) {
    $title = 'uBook';
    if ($search) {
        $title .= ' - Suche nach "' . $search . '"';
    }
    $link = WEBDIR;
    $desc = 'Neue Angebote bei uBook.';
    $lang = 'de-de';
    $copyright = 'uBook';
    $rss = new RssChannel($title, $link, $desc, $lang, $copyright);
    $imageUrl = 'http://ubook.asta-bielefeld.de/ubook_small.gif';
    $rss->addImage($imageUrl, $title, $link);
    $query = BookQuery::searchQuery($search);
    $query .= ' order by created desc';
    if ($limit > 0) {
        $query .= ' limit ' . $limit;
    }
    $mysqlResult = mysql_query($query);
    while ($book = Book::fromMySql($mysqlResult)) {
        $title = $book->get('title');
        $desc = 'Neues Buchangebot:' . "\n" . $book->asText();
        $desc = nl2br(Parser::text2html($desc));
        $id = $link = WEBDIR . 'book.php?id=' . $book->get('id');
        $author = 'ubook@asta-bielefeld.de (uBook-Team)';
        $date = $book->get('created');
        $rss->addItem($id, $title, $desc, $link, $author, $date);
    }
    return $rss;
}

/* Cleaning old books before searching */
require_once 'books/Cleaner.php';
Cleaner::checkOld();

$search = '';
$limit = 100;

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
if (isset($_GET['limit'])) {
    $limit = (int) $_GET['limit'];
}

$rss = create_rss($search, $limit);
$rss->send();
?>