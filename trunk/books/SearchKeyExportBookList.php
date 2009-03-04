<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'SearchKeyBookList.php';

require_once 'mysql_conn.php';
require_once 'tools/BookFetcher.php';
require_once 'tools/Parser.php';
require_once 'tools/WEBDIR.php';

class SearchKeyExportBookList extends SearchKeyBookList {

	const token = '<p>';

	private $textList = '';

	public function __construct($searchKey, $absoluteUrl = false) {
		$searchQuery = parent::searchQuery($searchKey->asText(), $searchKey->getOption());
		$result = mysql_query($searchQuery);
		parent::setSize(mysql_num_rows($result));
		parent::setHtmlRows('');
		if ($this->size() == 0) return;
		$this->formatBooks(&$result);
	}

	public function toTextList() {
		return $this->textList;
	}

	private function formatBooks($mysqlResult) {
		$list = '';
		$bookScriptUrl = WEBDIR . 'book.php';
		while ($book = BookFetcher::fetchHtml($mysqlResult)) {
			$list .= $bookScriptUrl . '?id=' . $book['id']
			. self::token . $book['author']
			. self::token . $book['title']
			. self::token . $book['price'] . "\n";
		}
		$this->textList = $list;
	}

}

?>