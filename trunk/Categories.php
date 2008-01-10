<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */

require_once 'mysql_conn.php';

/*
 * Books can be sorted by categories. Each book can be associated with zero or more categories.
 * 
 * You can access categories through this class. Read them, change them and define new ones.
 */
class Categories {
	
	var $categoryArray = null;
	
	/* this makes sure, that the table of categories is loaded */
	function Categories() {
		$this->loadFromDb();
	}
	
	/* returns an array with all categories */
	function getArray() {
		return $this->categoryArray;
	}
	
	/* get the id of a category by name */
	function indexOf($name) {
		return array_search($name, $this->categoryArray);
	}
	
	/* returns true, if a given category already exists */
	function exists($category) {
		$index = $this->indexOf($category);
		return !($index === false);
	}
	
	/* add a new category */
	function add($name) {
		$q = 'insert into categories (cat_name) values ("'.$name.'");';
		if (mysql_query($q)) {
			$id = mysql_last_inserted();
			$this->categoryArray[$i] = $name;
		}
	}
	
	function loadFromDb() {
		$this->categoryArray = array();
		$q = "select category from categories order by category;";
		$result = mysql_query($q);
		while ($row = mysql_fetch_array($result)) {
			$this->categoryArray[] = $row['category'];
		}
	}
	/*
	function defineNames() {
		self::$categoryArray = array(
		0 => 'Sonstiges',
		1 => 'Chemie',
		2 => 'Geschichte',
		3 => 'Philosophie',
		4 => 'Theologie',
		5 => 'Gesundheit',
		6 => 'Linguistik',
		7 => 'Literatur',
		8 => 'Kunst',
		9 => 'Musik',
		10 => 'Mathematik',
		11 => 'Pädagogik',
		12 => 'Physik',
		13 => 'Psychologie',
		14 => 'Sport',
		15 => 'Recht',
		16 => 'Soziologie',
		17 => 'Informatik',
		18 => 'Wirtschaft',
		19 => 'Biologie',
		20 => 'Medien',
		21 => 'Politik',
		22 => 'Sachbücher',
		23 => 'Kinder / Jugend',
		24 => 'Romane',
		25 => 'Reisen'
		);
	}
	*/
}
?>
