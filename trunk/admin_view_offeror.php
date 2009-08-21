<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

if (!isset($_GET['id'])) exit;

$id = (int) $_GET['id'];

$books = '';

require_once 'mysql_conn.php';
$query = 'select books.id, books.auth_key, books.author, books.title'
. ' from books books join books author_book on author_book.id = ' . $id
. ' and books.mail = author_book.mail'
. ' order by author, title, books.price';
$result = mysql_query($query);
while ($book = mysql_fetch_array($result)) {
	$books.= '<li>';
	$books.= '<a href="book.php?id='.$book['id'].'&amp;key='.$book['auth_key'].'">';
	$books.= $book['author'];
	$books.= ': ';
	$books.= $book['title'];
	$books.= '</a>';
	$books.= '</li>'."\n";
}

require 'header.php';

?>

  <div class="menu">
   <span><a href="admin.php">&larr; Zurück zur Administrationsübersicht</a></span>
  </div>
<h2>Bücher einer Anbieterin / eines Anbieters</h2>
<ol class="text">
 <?php echo $books; ?>
</ol>

<?php include 'footer.php'; ?>