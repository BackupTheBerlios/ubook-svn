<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';

require_once 'mysql_conn.php';

class OfferorsBookList extends AbstractBookList {

	public function __construct($bookId) {
		$searchQuery = self::searchQuery($bookId);
		$result = mysql_query($searchQuery);
		$books = parent::mysqlResultToHtml(&$result);
		parent::setHtmlRows($books);
	}

	/**
	 * Generates a MySQL select statement
	 *
	 * @param int $id an book id of the offeror
	 * @return MySQL select statement
	 */
	protected static function searchQuery($id) {
		$query = 'select books.id, books.author, books.title, books.price'
		. ' from books books join books author_book on author_book.id = ' . $id
		. ' and books.mail = author_book.mail'
		. ' order by author, title, books.price';
		return $query;
	}

}

?>