<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'func_book.php';
require_once 'Parser.php';
require_once 'WEBDIR.php';

/* creates a html table of books from a mysql result */
function format_books($mysql_result, $absoluteLink = false) {
	$books_string = '';
	$class = 0;
	$bookScriptUrl = 'book.php';
	if ($absoluteLink) {
		$bookScriptUrl = WEBDIR.$bookScriptUrl;
	}
	while ($book = fetch_book(&$mysql_result)) {
		Parser::htmlbook($book);
		$books_string .= '<tr class="bookrow'.$class.'"><td>';
		$books_string .= '<a href="'.$bookScriptUrl.'?id='.$book['id'].'"';
		if ($absoluteLink) {
			$books_string .= ' target="_blank"';
		}
		$books_string .= '>';
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
