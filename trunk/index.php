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

/* basic variables */
$search_key = null;
$category = '';


if (sizeof($_GET) == 0) {
	$http_equiv_expires = 43200;
}
else {
	/* Okay, dealing user input */
	/* requirements */
	
	function search_key() {
		if (!isset($_GET['search'])) return null;
		$key = trim($_GET['search']);
		return $key;
	}

	
	$search_key = search_key();
	
	if ($search_key !== null) {
		require_once 'books/SearchKeyBookList.php';
		$bookList = new SearchKeyBookList($search_key);
	}

	if (isset($_GET['cat'])) {
		$category = trim($_GET['cat']);
	}

	require_once 'books/CategoryBookList.php';
	$catBookList = new CategoryBookList($category);	

}


$navigation_links['first'] = array('Erste','./');
include 'header.php';

?>
<div class="menu">
   <span><b>Buch suchen</b></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
 </div>
 

   <form action="./" method="get" name="books">
    <input type="text" name="search" size="20" alt="Suchworte" style="width:20em; margin-bottom:0.4em;" value="<?php echo text2html(stripslashes($search_key)); ?>" />
    <script language="javascript" type="text/javascript">
     setFocus();
    </script>
    <br/>
    <input type="submit" value="Suchen" />
    <input type="submit" name="new" value="Neues" />
    <input type="submit" name="random" value="Zufälliges" />
  </form>
  <?php if ($search_key !== null) { ?>
   <h2>Suchergebnisse:</h2>
   <?php if ($bookList->size() == 0) { ?>
   <div>
	Es wurden keine Bücher gefunden.
   </div>
   <?php } else { ?>
    <?php echo $bookList->toHTML(); ?>
   <?php } ?>
  <?php } ?>




<?php echo categoryMenu(); ?>
 
  <?php if ($category != '') { ?>
   <h2><?php echo $category ?></h2>
   <?php if ($catBookList->size() == 0) { ?>
   <div>
	In dieser Kategorie gibt es zur Zeit keine Bücher.
   </div>
   <?php } else { ?>
    <?php echo $catBookList->toHTML(); ?>
   <?php } ?>
  <?php } ?>
 
 
 
 
<?php include 'footer.php'; ?>