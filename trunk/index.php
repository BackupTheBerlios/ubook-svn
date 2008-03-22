<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

require_once 'magic_quotes.php';
require_once 'func_text2html.php';

function search_key() {
	if (!isset($_GET['search'])) return '';
	$key = trim($_GET['search']);
	// $key = str_replace(':','',$key);
	return $key;
}

function search_query($search_key) {
	$option = false;
	if (isset($_GET['option'])) $option = $_GET['option'];
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

$search_key = search_key();

$books = '';
if ($search_key) {
	require_once 'mysql_conn.php';
	require_once 'func_book.php';
	require_once 'func_format_books.php';
	$result = mysql_query(search_query($search_key));
	$numberOfRows = mysql_num_rows($result);
	$books = format_books(&$result);
	$countResult = mysql_query('select count(id) from books;');
	list($null, $numberOfAllBooks) = each(mysql_fetch_row($countResult));
}

if (trim($search_key) == '') {
	$http_equiv_expires = 43200;
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
    <input type="hidden" name="option" value="<?php echo $_GET['option']; ?>" />
    <input type="text" name="search" size="20" alt="Suchworte" style="width:20em; margin-bottom:0.4em;" value="<?php echo text2html(stripslashes($search_key)); ?>" />
    <script language="javascript" type="text/javascript">
     setFocus();
    </script>
    <br/>
    <input type="submit" value="Suchen" />
  </form>
 <div class="menu" style="margin-top:1em;">
   <span><a href="index.php?search=%&amp;option=new" title="Zeigt die sieben neuesten Suchergebnisse an.">Was ist neu?</a></span>
   <span><a href="index.php?search=%&amp;option=random" title="Zeigt sieben zufällige Suchergebnisse an.">Überrasch mich.</a></span>
   <span><a href="category.php" title="Hier kannst du alle Bücher in Kategorien aufgeteilt finden.">Kategorien!</a></span>
 </div>
  <br clear="all" />
  <?php if (trim($search_key) != '') { ?>
   <h2>Suchergebnisse:</h2>
   <?php if ($numberOfRows == 0) { ?>
   <div>
	Es wurden keine Bücher gefunden.
   </div>
   <?php } else { ?>
   <div class="results">
    <table align="center" style="text-align:left">
     <?php echo $books; ?>
    </table>
   </div>
   <div style="margin-top: 0.3em;" title="Summe angezeigter Bücher / Summe der Bücher insgesamt">
   <?php echo $numberOfRows; ?> / <?php echo $numberOfAllBooks; ?>
   </div>
   <?php } ?>
  <?php } ?>
<?php include 'footer.php'; ?>