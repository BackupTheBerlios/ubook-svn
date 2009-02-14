<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'BookList.php';

abstract class AbstractBookList implements BookList {

	private static $numberOfAllBooks = null;

	private $numberOfRows;
	private $booksAsHTML;
	
	public function size() {
		return $this->numberOfRows;
	}
	
	public function toHTML() {
		return $this->booksAsHTML;
	}

	protected function setSize($size) {
		$this->numberOfRows = $size;
	}

	protected function setHtmlRows($htmlRows) {
		$this->booksAsHTML = $this->bookTable($htmlRows);
	}

	protected function bookTable($bookRows) {
		$t = '<div class="results">
    			<table align="center" style="text-align:left">';
		$t .= $bookRows;
		$t .= '</table>
   			</div>
		   	<div style="margin-top: 0.3em;" title="Summe angezeigter Bücher / Summe der Bücher insgesamt">';
		$t .= $this->size().' / '.self::numberOfAllBooks();
		$t .= '</div>';
		return $t;
	}

	private static function numberOfAllBooks() {
		if (self::$numberOfAllBooks === null) {
			$countResult = mysql_query('select count(id) from books;');
			list($null, self::$numberOfAllBooks) = each(mysql_fetch_row($countResult));
		}
		return self::$numberOfAllBooks;
	}

}

?>