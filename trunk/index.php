<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */
if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'func_text2html.php';
require_once 'Categories.php';
require_once 'books/SearchKey.php';
require_once 'books/BookList.php';

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

	if ($searchKey->isGiven()) {
		require_once 'books/SearchKeyBookList.php';
		$bookList = new SearchKeyBookList($searchKey);

		if ($bookList->size() == 0) {
			/* Nothing found here, ask other servers. */
			require_once 'net/ExternalBookList.php';
			require_once 'net/ExternalServerPool.php';
			require_once 'net/ThreadedBookListReader.php';
				
			function load_externalBookListArray($searchKey) {
				$serverPool = new ExternalServerPool(true);
				$reader = new ThreadedBookListReader($serverPool, $searchKey);
				return $reader->read();
			}
			
			$externalBookListArray = load_externalBookListArray($searchKey);
		}
	}

	if (isset($_GET['cat'])) {
		require_once 'books/CategoryBookList.php';
		$category = trim($_GET['cat']);
		$catBookList = new CategoryBookList($category);
	}

}


$navigation_links['first'] = array('Erste','./');
include 'header.php';

?>
<div class="menu"><span><b>Buch suchen</b></span> <span><a
	href="add.php">Buch anbieten</a></span> <span><a href="help.php">Hilfe</a></span>
<span><a href="about.php">Impressum</a></span></div>


<form action="./" method="get" name="books"><input type="text"
	name="search" size="20" alt="Suchworte"
	style="width: 20em; margin-bottom: 0.4em;"
	value="<?php echo $searchKey->asHtml(); ?>" /> <script
	language="javascript" type="text/javascript">
     setFocus();
    </script> <br />
<input type="submit" value="Suchen" /> <input type="submit" name="new"
	value="Neues" /> <input type="submit" name="random" value="Zufälliges" />
</form>


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
<div style="margin-top: 0.3em;" title="Summe angezeigter Bücher / Summe der Bücher insgesamt"> 
<?php echo $bookList->size(); ?> / <?php echo AbstractBookList::numberOfAllBooks(); ?>
</div>
</div>
<?php } ?>
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
<div style="margin-top: 0.3em;" title="Summe angezeigter Bücher / Summe der Bücher insgesamt"> 
<?php echo $catBookList->size(); ?> / <?php echo AbstractBookList::numberOfAllBooks(); ?>
</div>
</div>
<?php } ?>
<?php } ?>


<?php include 'footer.php'; ?>