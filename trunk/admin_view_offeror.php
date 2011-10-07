<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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