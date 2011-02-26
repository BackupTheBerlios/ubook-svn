<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';
require_once 'books/Book.php';
require_once 'tools/Mailer.php';

/**
 * Holds a list of books.
 * @author maikel
 *
 */
class UsersBooks {

	var $bookCount;
	var $bookListString;

	/**
	 * Queries the book list from the database and stores it.
	 * @param $userMail A valid mail address. Quits if none given.
	 * @return UsersBooks
	 */
	function UsersBooks($userMail) {
		if (!$userMail) exit;
		$query =
		'select
		 id, author, title, price, year, description, auth_key
		 from books where mail="'.$userMail.'"
		 order by author, title, price';
		$bookListResult = mysql_query($query);
		$this->bookCount = mysql_num_rows($bookListResult);
		$listString = "\n";
		while ($book = Book::fromMySql($bookListResult)) {
			$listString .= "\n";
			$listString .= $book->get('author').': '.$book->get('title')."\n";
			$listString .= Mailer::editLink($book->get('id'), $book->get('auth_key'))."\n";
		}
		$this->bookListString = $listString;
	}

	/**
	 * Number of books.
	 * @return int number of books
	 */
	function size() {
		return (int) $this->bookCount;
	}

	/**
	 * Returns the full book list as string.
	 * @return string simple plain text format
	 */
	function toString() {
		return $this->bookListString;
	}

}

?>