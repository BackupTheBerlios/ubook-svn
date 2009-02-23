<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';

require_once 'mysql_conn.php';

class CategoryBookList extends AbstractBookList {
	
	public function __construct($category) {
		$books = $this->booksInCat($category);
		parent::setHtmlRows($books);
	}
	
	private function booksInCat($category) {
		$books = '';
		if ($category) {
			$q = 'select books.id, books.author, books.title, books.price from books';
			if ($category == 'Sonstiges') {
				$q .= ' left join book_cat_rel
		 on books.id=book_cat_rel.book_id 
		 where book_cat_rel.category="'.$category.'" 
		 or book_cat_rel.category is null';
			}
			else {
				$q .= ' join book_cat_rel
		 on books.id=book_cat_rel.book_id 
		 where book_cat_rel.category="'.$category.'"';
			}
			$q .= ' order by books.author, books.title, books.price;';
			$result_books = mysql_query($q);
			parent::setSize(mysql_num_rows($result_books));
			$books = parent::mysqlResultToHtml(&$result_books);
		}
		return $books;
	}

}

?>