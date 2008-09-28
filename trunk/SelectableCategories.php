<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */

require_once 'Categories.php';
require_once 'mysql_conn.php';

/*
 * Creates select fields for html forms.
 */
class SelectableCategories {
	
	var $categories;
	var $book_id;
	var $book_cats;
	
	/* this makes sure, that the table of categories is loaded */
	function SelectableCategories($book_id=0) {
		$this->categories = new Categories();
		$this->setBookId($book_id);
		$this->loadBookCats();
	}
	
	function createSelect($index) {
		$cats = $this->categories->getArray();
		if (isset($this->book_cats[$index])) {
			$book_cat = $this->book_cats[$index];
		}
		$select = '<select name="categories['.$index.']" size="1">';
		$select .= '<option></option>';
		foreach ($cats as $index => $category) {
			$option = '<option';
			if ($category == $book_cat) {
				$option .= ' selected="selected"';
			}
			$option .= '>';
			$option .= $category;
			$option .= '</option>';
			$select .= $option;
		}
		$select .= '</select>';
		return $select;
	}
	
	// started a method for a number of selects depending on the number of categories
	/*
	function createSelectArray() {
		$size = 2;
	}
*/
	/* update the database from POST form data */
	function update() {
		if (!isset($_POST['categories'])) return;
		$new_cats = $_POST['categories'];
		if (count($new_cats) != 2) return;
		$old_cats = $this->book_cats;
		$to_delete = array_diff($old_cats,$new_cats);
		$to_add = array_diff($new_cats,$old_cats);
		if (count($to_delete) > 0) {
			$q = 'delete from book_cat_rel where book_id="'.$this->book_id.'"';
			mysql_query($q);
			$to_add = $new_cats;
		}
		foreach ($to_add as $index => $category) {
			if (!trim($category)) continue;
			if (!$this->categories->exists(stripslashes($category))) continue;
			$q = 'insert into book_cat_rel (book_id, category)
				values ("'.$this->book_id.'", "'.$category.'")';
			mysql_query($q);
		}
	}
	
	function setBookId($book_id) {
		$this->book_id = $book_id;
	}
	
	function loadBookCats() {
		$this->book_cats = array();
		if ($this->book_id == 0) return;
		$q = 'select category from book_cat_rel where book_id="'.$this->book_id.'"';
		$result = mysql_query($q);
		while ($row = mysql_fetch_array($result)) {
			$this->book_cats[] = $row['category'];
		}
	}
	
}
?>
