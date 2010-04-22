<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'books/AbstractBookList.php';
require_once 'Message.php';

class ExternalBookList extends AbstractBookList {

	private $from;
	private $books;
	
	public function __construct($from, $bookList) {
		$this->from = $from;
		$this->books = $bookList;
		$this->formatBooks($bookList);
	}

	public function locationName() {
		return $this->from;
	}

	public function size() {
		return sizeof($this->books);
	}

	private function formatBooks($books) {
		$books_string = '';
		foreach ($books as $i => $book) {
			$books_string .= '<tr><td>';
			$books_string .= '<a href="'.$book->getUrl().'" target="_blank">';
			if ($book->getAuthor()) {
				$books_string .= $book->getAuthor();
				$books_string .= ': ';
			}
			$books_string .= $book->getTitle();
			$books_string .= '</a>';
			$books_string .= '</td><td>'.$book->getPrice().' &euro;</td></tr>'."\n";
		}
		parent::setHtmlRows($books_string);
	}

}

?>