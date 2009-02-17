<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */
 
$books = '';

require_once 'mysql_conn.php';
$query = 'select id, auth_key, author, title from books order by author, title, price';
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
<h2>Alle Bücher</h2>
<ol class="text">
 <?php echo $books; ?>
</ol>
  
 </body> 
</html>
