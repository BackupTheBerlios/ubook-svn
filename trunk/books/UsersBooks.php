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
		 from books where mail="'.addslashes($userMail).'"
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