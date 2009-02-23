<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/AbstractBookList.php';
require_once 'Message.php';

class ExternalBookList extends AbstractBookList {

	private $from = '';
	
	public function __construct($from, $textList) {
		$this->from = $from;
		$this->parseList($textList);
	}

	public function locationName() {
		return $this->from;
	}

	private function parseList($textList) {
		$books = array();
		$lineArray = split("\n", $textList);
		foreach ($lineArray as $i => $bookLine) {
			$bookArray = split('<p>', $bookLine);
			if (sizeof($bookArray) != 4) {
				continue;
			}
			if (self::hasBadCharacters($bookArray)) {
				continue;
			}
			$books[] = $bookArray;
		}
		parent::setSize(sizeof($books));
		$this->formatBooks($books);
	}

	private function formatBooks($books) {
		$books_string = '';
		foreach ($books as $i => $book) {
			$books_string .= '<tr><td>';
			$books_string .= '<a href="'.$book[0].'" target="_blank">';
			if ($book[1]) {
				$books_string .= $book[1];
				$books_string .= ': ';
			}
			$books_string .= $book[2];
			$books_string .= '</a>';
			$books_string .= '</td><td>'.$book[3].' &euro;</td></tr>'."\n";
		}
		parent::setHtmlRows($books_string);
	}

	private static function hasBadCharacters($stringArray) {
		foreach ($stringArray as $i => $s) {
			if (Message::hasBadChar($s)) {
				return true;
			}
		}
		return false;
	}

}

?>