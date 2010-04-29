<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'SearchKeyBookList.php';

require_once 'mysql_conn.php';
require_once 'tools/BookFetcher.php';
require_once 'tools/Parser.php';
require_once 'tools/WEBDIR.php';

class SearchKeyExportBookList extends SearchKeyBookList {

	private $list = array();

	public function __construct($searchKey, $absoluteUrl = false) {
		$searchQuery = parent::searchQuery($searchKey->asText(), $searchKey->getOption());
		$result = mysql_query($searchQuery);
		parent::setSize(mysql_num_rows($result));
		parent::setHtmlRows('');
		if ($this->size() == 0) return;
		$this->formatBooks(&$result);
	}

	public function getList() {
		return $this->list;
	}

	private function formatBooks($mysqlResult) {
		$bookScriptUrl = WEBDIR . 'book.php';
		while ($book = BookFetcher::fetchHtml($mysqlResult)) {
			$url = $bookScriptUrl . '?id=' . $book['id'];
			$extBook = new ExternalBook($url, $book['author'], $book['title'], $book['price']);
			$this->list[] = $extBook;
		}
	}

}

?>