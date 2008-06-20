<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'mysql_conn.php';
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
	require_once 'func_book.php';
	require_once 'func_format_books.php';
	
	/**
	 * Generates a MySQL select statement
	 *
	 * @param string $search_key user given search key
	 * @return MySQL select statement
	 */
	function search_query($search_key) {
		$option = false;
		if (isset($_GET['new'])) $option = 'new';
		if (isset($_GET['random'])) $option = 'random';
		//$data_fields = array('author','title','description');
		$fields = 'concat(author," ",title," ",description) ';
		$keys = explode(' ',$search_key);
		//$or = '(';
		$and = ' ';
		$query = 'select id, author, title, price from books where ';
		foreach ($keys as $i => $k) {
			$query .= $and.$fields.'like "%'.$k.'%"';
			$and = ' and ';
		}
		if ($option == 'new') {
			$query .= ' order by created desc limit 7';
		}
		else if ($option == 'random') {
			$query .= ' order by rand() limit 7';
		}
		else {
			$query .= ' order by author, title, price';
		}
		return $query;
	}


	function search_key() {
		if (!isset($_GET['search'])) return null;
		$key = trim($_GET['search']);
		return $key;
	}

	
	function booksInCat($category) {
		global $numberOfRows;
		$books = '';
		if ($category) {
			$q = 'select books.id, books.author, books.title, books.price from books';
			if ($category == 'Sonstiges') {
				$q .= ' left join book_cat_rel
		 on books.id=book_cat_rel.book_id 
		 where book_cat_rel.category="'.$category.'" 
		 or book_cat_rel.category is null';
			}
			else {
				$q .= ' join book_cat_rel
		 on books.id=book_cat_rel.book_id 
		 where book_cat_rel.category="'.$category.'"';
			}
			$q .= ' order by books.author, books.title, books.price;';
			$result_books = mysql_query($q);
			$numberOfRows = mysql_num_rows($result_books);
			$books = format_books(&$result_books);
		}
		return $books;
	}

	function bookTable($bookRows) {
		global $numberOfRows;
		global $numberOfAllBooks;
		$t = '<div class="results">
    			<table align="center" style="text-align:left">';
     	$t .= $bookRows;
    	$t .= '</table>
   			</div>
		   	<div style="margin-top: 0.3em;" title="Summe angezeigter Bücher / Summe der Bücher insgesamt">';
   		$t .= $numberOfRows.' / '.$numberOfAllBooks;
   		$t .= '</div>';
   		return $t;
	}
	
	$search_key = search_key();
	
	$books = '';

	if ($search_key !== null) {
		$result = mysql_query(search_query($search_key));
		$numberOfRows = mysql_num_rows($result);
		$books = format_books(&$result);
	}

	if (isset($_GET['cat'])) {
		$category = trim($_GET['cat']);
	}

	$catBooks = booksInCat($category);

	$countResult = mysql_query('select count(id) from books;');
	list($null, $numberOfAllBooks) = each(mysql_fetch_row($countResult));
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
    <!-- input type="hidden" name="option" value="<?php echo $_GET['option']; ?>" /> -->
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
   <?php if ($numberOfRows == 0) { ?>
   <div>
	Es wurden keine Bücher gefunden.
   </div>
   <?php } else { ?>
    <?php echo bookTable($books); ?>
   <?php } ?>
  <?php } ?>




<?php echo categoryMenu(); ?>
 
  <?php if ($category != '') { ?>
   <h2><?php echo $category ?></h2>
   <?php if ($catBooks == '') { ?>
   <div>
	In dieser Kategorie gibt es zur Zeit keine Bücher.
   </div>
   <?php } else { ?>
    <?php echo bookTable($catBooks); ?>
   <?php } ?>
  <?php } ?>
 
 
 
 
<?php include 'footer.php'; ?>