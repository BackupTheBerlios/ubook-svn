<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'func_text2html.php';
require_once 'Categories.php';

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

class SearchKey {

	private $key = null;

	public function __construct() {
		$this->get();
	}

	public function isGiven() {
		if ($this->key === null) return false;
		else return true;
	}

	public function asText() {
		return $this->key;
	}

	public function asHtml() {
		return text2html(stripslashes($this->key));
	}

	public function getOption() {
		if (isset($_GET['new'])) return 'new';
		if (isset($_GET['random'])) return 'random';
		return false;
	}

	private function get() {
		if (isset($_GET['search'])) {
			$this->key = trim($_GET['search']);
		}
	}

}

/* basic variables */
$category = '';
$searchKey = new SearchKey();


if (sizeof($_GET) == 0) {
	$http_equiv_expires = 43200;
}
else {
	/* Okay, dealing user input */
	/* requirements */

	if ($searchKey->isGiven()) {
		require_once 'books/SearchKeyBookList.php';
		$bookList = new SearchKeyBookList($searchKey);
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
<h2>Suchergebnisse:</h2>
<?php if ($bookList->size() == 0) { ?>
<div>Es wurden keine Bücher gefunden.</div>
<?php } else { ?>
<?php echo $bookList->toHTML(); ?>
<?php } ?>
<?php } ?>


<?php echo categoryMenu(); ?>

<?php if ($category != '') { ?>
<h2><?php echo $category ?></h2>
<?php if ($catBookList->size() == 0) { ?>
<div>In dieser Kategorie gibt es zur Zeit keine Bücher.</div>
<?php } else { ?>
<?php echo $catBookList->toHTML(); ?>
<?php } ?>
<?php } ?>


<?php include 'footer.php'; ?>