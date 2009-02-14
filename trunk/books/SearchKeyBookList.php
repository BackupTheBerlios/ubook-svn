<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';

require_once 'mysql_conn.php';
require_once 'func_format_books.php';

class SearchKeyBookList extends AbstractBookList {
	
	public function __construct($searchKey, $absoluteUrl = false) {
		$searchQuery = self::searchQuery($searchKey->asText(), $searchKey->getOption());
		$result = mysql_query($searchQuery);
		parent::setSize(mysql_num_rows($result));
		$books = format_books(&$result, $absoluteUrl);
		parent::setHtmlRows($books);
	}
	
	/**
	 * Generates a MySQL select statement
	 *
	 * @param string $search_key user given search key
	 * @return MySQL select statement
	 */
	private static function searchQuery($search_key, $option) {
		$fields = 'concat(author," ",title," ",description) ';
		$keys = explode(' ',$search_key);
		$and = ' ';
		$query = 'select id, author, title, price from books where ';
		foreach ($keys as $i => $k) {
			$query .= $and.$fields.'like "%'.$k.'%"';
			$and = ' and ';
		}
		if ($option == 'new') {
			$query .= ' order by created desc limit 7';
		}
		else if ($option == 'random') {
			$query .= ' order by rand() limit 7';
		}
		else {
			$query .= ' order by author, title, price';
		}
		return $query;
	}

}

?>