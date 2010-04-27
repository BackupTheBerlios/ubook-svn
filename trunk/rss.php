<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

include_once 'magic_quotes.php';
require_once 'books/BookQuery.php';
require_once 'tools/BookFetcher.php';
require_once 'tools/BookFormatter.php';
require_once 'tools/RssChannel.php';
require_once 'tools/WEBDIR.php';

function create_rss($search) {
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
    $mysqlResult = mysql_query($query);
    while ($book = BookFetcher::fetch($mysqlResult)) {
        $title = $book['title'];
        $desc = 'Neues Buchangebot:' . "\n" . BookFormatter::asText($book);
        $desc = str_replace("\n", "<br />\n", $desc);
        $id = $link = WEBDIR . 'book.php?id=' . $book['id'];
        $author = 'ubook@asta-bielefeld.de (uBook-Team)';
        $date = $book['created'];
        $rss->addItem($id, $title, $desc, $link, $author, $date);
    }
    return $rss;
}

/* Cleaning old books before searching */
require_once 'books/Cleaner.php';
Cleaner::checkOld();

$search = '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$rss = create_rss($search);
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