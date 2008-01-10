<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'func_text2html.php';
require_once 'func_format_books.php';
require_once 'Categories.php';

function categoryMenu() {
	$categories = new Categories();
	$cat_arr = $categories->getArray();
	$menu =	'<div class="categories">';
	foreach ($cat_arr as $index => $category) {
		$menu .= '<span><a href="category.php?cat=';
		$menu .= $category;
		$menu .= '">';
		$menu .= $category;
		$menu .= '</a></span> ';
	}
	$menu .= '</div>';
	return $menu;
}

$category = '';
$books = '';
if (isset($_GET['cat'])) {
	$category = trim($_GET['cat']);
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
	$books = format_books(&$result_books);
}

$navigation_links['first'] = array('Erste','category.php');
include 'header.php';
?>
<div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
</div>
<p><?php echo categoryMenu(); ?></p>

  <br clear="all" />
  <?php if ($category != '') { ?>
   <h2><?php echo $category ?></h2>
   <?php if ($books == '') { ?>
   <div>
	In dieser Kategorie gibt es zur Zeit keine Bücher.
   </div>
   <?php } else { ?>
   <div class="results">
    <table align="center" style="text-align:left">
     <?php echo $books; ?>
    </table>
   </div>
   <?php } ?>
  <?php } else { ?>
   Wähle eine Kategorie.
  <?php } ?>
<?php include 'footer.php'; ?>