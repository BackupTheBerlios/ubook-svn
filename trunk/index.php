<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */
/*
 * TODO and further ideas
 * A collection of ideas, what should or could be done in the future.
 *
 * - image upload fails with some (too big?) images (see berlios ticket)
 * - <form> must not contain <form>.
 * - mirgration from XHTML 1.0 to 1.1
 * - statistics:
 *   - percentage of books with image
 *   - distribution of books in the time (created, expire, lifetime)
 *   - books per person
 *   - number of stored mail addresses
 *   - active books / total number of books (with deleted ones)
 * - feedback, if a wrong mail address was typed in
 * - (Bielefeld) category 'Sprachbücher', because of a lot of language lectures
 * - (Bielefeld) categories of the FH
 * - new category schema, perhaps a tree?
 */
if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'books/SearchKey.php';
require_once 'books/BookList.php';
require_once 'tools/Categories.php';

function categoryMenu() {
	$categories = new Categories();
	$cat_arr = $categories->getArray();
	$menu =	'<div class="categories" style="width:30em; margin:0px auto; margin-top:1em;">Kategorien: ';
	foreach ($cat_arr as $index => $category) {
		$menu .= '<span><a href="?cat=';
		$menu .= $category;
		$menu .= '">';
		$menu .= $category;
		$menu .= '</a></span> ';
	}
	$menu .= '</div>';
	return $menu;
}

/* basic variables */
$category = '';
$searchKey = new SearchKey();


if (sizeof($_GET) == 0) {
	$http_equiv_expires = 43200;
}
else {
	/* Okay, dealing user input */

	/* Cleaning old books before searching */
	require_once 'books/Cleaner.php';
	Cleaner::checkOld();

	if ($searchKey->isGiven()) {
		require_once 'books/SearchKeyBookList.php';
		$bookList = new SearchKeyBookList($searchKey);

		if ($bookList->size() == 0) {
			/* Nothing found here, ask other servers. */
			require_once 'net/ExternalBookList.php';
			require_once 'net/ExternalServerPool.php';
			require_once 'net/ThreadedBookListReader.php';

			function load_externalBookListArray($searchKey) {
				$serverPool = ExternalServerPool::activeServerPool();
				$reader = new ThreadedBookListReader($serverPool, $searchKey);
				return $reader->read();
			}

			$externalBookListArray = load_externalBookListArray($searchKey);
		}

		$feedUrl = WEBDIR . 'rss.php?search=' . urlencode($searchKey->asText());
		$feedLink = '<link rel="alternate" type="application/rss+xml"'
		. ' title="RSS" href="' . $feedUrl . '" />';
	}

	if (isset($_GET['cat'])) {
		require_once 'books/CategoryBookList.php';
		$category = trim($_GET['cat']);
		$catBookList = new CategoryBookList($category);
	}

}


$navigation_links['first'] = array('Erste','./');
define('FOCUS', true);
//define('AUTOCOMPLETER', true);
include 'header.php';

?>
<div class="menu"><span><b>Buch suchen</b></span> <span><a
	href="add.php">Buch anbieten</a></span> <span><a href="help.php">Tipps</a></span>
<span><a href="about.php">Impressum</a></span></div>


<form action="./" method="get" name="books"><input type="text"
	name="search" id="search" size="20" alt="Suchworte"
	value="<?php echo $searchKey->asHtml(); ?>" /> <script
	type="text/javascript">
     setFocus();
    </script> <br />
<input type="submit" value="Suchen" /> <input type="submit" name="new"
	value="Neues" /> <input type="submit" name="random" value="Zufälliges" />
</form>

<?php if (isset($_GET['searchSaved']) && $_GET['searchSaved']) { ?>
<div class="infobox">Diese Suche wurde abonniert.</div>
<?php } ?>
<?php if (isset($_GET['searchDeleted']) && $_GET['searchDeleted']) { ?>
<div class="infobox">Das Abonnement für diese Suche wurde beendet.</div>
<?php } ?>

<?php if ($searchKey->isGiven()) { ?>
<?php if ($bookList->size() == 0) { ?>
<?php if (sizeof($externalBookListArray) > 0) { ?>
<h2>Suchergebnisse aus anderen Orten:</h2>
<div>Hier wurden keine Bücher gefunden. Stattdessen werden Suchergebnisse
von anderen Standorten angezeigt.</div>
<div class="results"><div class="external_results">
<table align="center">
<?php foreach ($externalBookListArray as $i => $externalBookList) { ?>
<tr><th colspan="2"><?php echo $externalBookList->locationName(); ?></th></tr>
<?php echo $externalBookList->toHtmlRows(); ?>
<?php } ?>
</table>
</div></div>
<?php } else { ?>
<h2>Keine Bücher gefunden</h2>
<p>Es wurden keine Bücher gefunden.</p>
<?php } ?>
<?php } else { ?>
<h2>Suchergebnisse:</h2>
<div class="results">
<table align="center">
<?php echo $bookList->toHtmlRows(); ?>
</table>
<div style="margin-top: 0.3em;" title="Summe gefundener Bücher / Summe aller Bücher insgesamt">
    [ Gefunden: <?php echo $bookList->size(); ?> ][ Insgesamt: <?php echo AbstractBookList::numberOfAllBooks(); ?> ]
</div>
</div>
<?php } ?>
<div class="notificationSubscription">
	<a href="<?php echo $feedUrl; ?>">Diese Suche als RSS-Feed</a>
</div>
<?php
    if (!isset($_GET['searchSaved']) && !$searchKey->getOption()) {
        require_once 'notification/Searches.php';
        $searches = new Searches();
        if ($searches->areActivated()) {
?>
<div class="notificationSubscription">
<form action="save_search.php" method="get">
 <input type="hidden" name="search" value="<?php echo $searchKey->asHtml(); ?>" />
 <input type="submit" value="Diese Suche abonnieren" />
</form>
</div>
<?php
        }
    }
?>
<?php } ?>


<?php echo categoryMenu(); ?>

<?php if ($category != '') { ?>
<h2><?php echo $category ?></h2>
<?php if ($catBookList->size() == 0) { ?>
<div>In dieser Kategorie gibt es zur Zeit keine Bücher.</div>
<?php } else { ?>
<div class="results">
<table align="center">
<?php echo $catBookList->toHtmlRows(); ?>
</table>
<div style="margin-top: 0.3em;" title="Summe gefundener Bücher / Summe aller Bücher insgesamt">
    [ Gefunden: <?php echo $catBookList->size(); ?> ][ Insgesamt: <?php echo AbstractBookList::numberOfAllBooks(); ?> ]
</div>
</div>
<?php } ?>
<?php } ?>


<?php include 'footer.php'; ?>