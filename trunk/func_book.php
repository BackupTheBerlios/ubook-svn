<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'Parser.php';

function fetch_book($mysql_result) {
	$book = mysql_fetch_array($mysql_result);
	if ($book == null) return;
	$book['price'] = str_replace('.',',',$book['price']);
	return $book;
}

?>