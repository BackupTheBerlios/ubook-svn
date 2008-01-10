<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */

require_once 'func_book.php';

/* creates a html table of books from a mysql result */
function format_books($mysql_result) {
	$books_string = '';
	$class = 0;
	while ($book = fetch_book(&$mysql_result)) {
		$books_string .= '<tr class="bookrow'.$class.'"><td>';
		$books_string .= '<a href="book.php?id='.$book['id'].'">';
		if ($book['author']) {
			$books_string .= $book['author'];
			$books_string .= ': ';
		}
		$books_string .= $book['title'];
		$books_string .= '</a>';
		$books_string .= '</td><td>'.$book['price'].' &euro;</td></tr>'."\n";
		$class = (int) !$class;
	}
	return $books_string;
}

?>
