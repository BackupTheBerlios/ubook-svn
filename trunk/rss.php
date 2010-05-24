<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
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
        $title .= ' - Suche nach "'.$search.'"';
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
/*?>

<rss version="2.0">

    <channel>
        <title>uBook<?php echo $search?' - Suche nach "'.$search.'"':''; ?></title>
        <link>http://ubook.asta-bielefeld.de/</link>
        <description>Neue Angebote bei uBook.</description>
        <language>de-de</language>
        <copyright>uBook</copyright>
        <pubDate>Mon, 22 Jun 2009 2:42:23</pubDate>

        <image>
            <url>http://ubook.asta-bielefeld.de/ubook_small.gif</url>
            <title>uBook-Logo</title>
            <link>http://ubook.asta-bielefeld.de/</link>
        </image>

        <?php echo $books; ?>
        <item>
            <guid>2009-08-06_Solidaritat</guid>
            <pubDate>Thu, 06 Aug 2009 23:27:00</pubDate>
        </item>

    </channel>
</rss>
 *
*/
?>