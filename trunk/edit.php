<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */

function format_categories_of(&$book) {
	$id = $_GET['id'];
	$q = 'select category from book_cat_rel where book_id="'.$id.'"';
}

require_once 'mysql_conn.php';
require_once 'func_book.php';
require_once 'SelectableCategories.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

$query = 'select
 author, title, year, price, expires, description 
 from books where id="'.$id.'" and auth_key="'.$key.'"';
$result = mysql_query($query);
if (mysql_num_rows($result) == 0) {
	$error = 'not found';
} else {
	/* we have valid access to this book */
	$selectableCategories = new SelectableCategories($id);

	if (isset($_POST['author'])) {
		/* update base book data */
		$query = 'update books set
  		author = "'.$_POST['author'].'",
  		title = "'.$_POST['title'].'",
  		year = "'.$_POST['year'].'",
  		price = "'.str_replace(',','.',$_POST['price']).'",
  		description = "'.$_POST['description'].'"
	     where id="'.$id.'" and auth_key="'.$key.'"';
		mysql_query($query);
		/* update category relations */
		$selectableCategories->update();
		/* update expire date and look at the book */
		require 'renew.php';
	}

	$book = mysql_fetch_array($result);
	format_book($book);
}

include 'header.php';

?>
  <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
 </div>
  <p>
   <form action="edit.php?id=<?php echo $_GET['id']; ?>&key=<?php echo $_GET['key']; ?>" method="POST">
    <table style="width:20em; border:gray;solid;1px;" align="center">
     <tr><td>Autor:</td><td><input type="text" name="author" value="<?php echo $book['author']; ?>" /></td></tr>
     <tr><td>Titel:</td><td><input type="text" name="title" value="<?php echo $book['title']; ?>" /></td></tr>
     <tr><td>Preis:</td><td><input type="text" name="price" value="<?php echo $book['price']; ?>" size="5" /> Euro</td></tr>
     <tr><td>Erscheinungsjahr:</td><td><input type="text" name="year" value="<?php echo $book['year']; ?>" size="4" /></td></tr>
     <tr><td>Kategorien:</td><td><?php echo $selectableCategories->createSelect(0); ?></td></tr>
     <tr><td>&nbsp;</td><td><?php echo $selectableCategories->createSelect(1); ?></td></tr>
     <tr><td colspan="2">Weiteres:<br /><textarea name="description" cols="25" rows="10"><?php echo $book['description']; ?></textarea></td></tr>
    </table>
    <p><input type="submit" value="Speichern" /></p>
   </form>
  </p>
 </body>
</html>
