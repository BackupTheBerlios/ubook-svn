<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

function fetch_book($mysql_result) {
	$book = mysql_fetch_array($mysql_result);
	if ($book == null) return;
	$book['price'] = str_replace('.',',',$book['price']);
	return $book;
}

?>