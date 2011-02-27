<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'Categories.php';
require_once 'text/Template.php';
require_once 'mysql_conn.php';

/**
 * Creates select fields for html forms.
 */
class SelectableCategories {

    private $categories;
    private $bookId;
    private $bookCats;

    /* this makes sure, that the table of categories is loaded */
    public function __construct($bookId = 0) {
        $this->categories = new Categories();
        $this->setBookId($bookId);
        $this->loadBookCats();
    }

    // started a method for a number of selects depending on the number of categories

    public function createSelectArray($selectedCats = null) {
        if (!is_array($selectedCats)) {
            $selectedCats = $this->bookCats;
        }
        $size = $this->numberOfSelectableCategories();
        $selectArray = array();
        for ($i=0; $i<$size; $i++) {
            $selectArray[] = $this->createSelect($i, $selectedCats);
        }
        return $selectArray;
    }

    /* update the database from POST form data */
    public function update() {
        if (!isset($_POST['categories'])) return;
        $new_cats = $_POST['categories'];
        if (count($new_cats) != $this->numberOfSelectableCategories()) return;
        $old_cats = $this->bookCats;
        $to_delete = array_diff($old_cats,$new_cats);
        $to_add = array_diff($new_cats,$old_cats);
        if (count($to_delete) > 0) {
            $q = 'delete from book_cat_rel where book_id="'.$this->bookId.'"';
            mysql_query($q);
            $to_add = $new_cats;
        }
        foreach ($to_add as $index => $category) {
            if (!trim($category)) continue;
            if (!$this->categories->exists(stripslashes($category))) continue;
            $q = 'insert into book_cat_rel (book_id, category)
				values ("'.$this->bookId.'", "'.$category.'")';
            mysql_query($q);
        }
    }

    public function setBookId($bookId) {
        $this->bookId = $bookId;
    }

    private function createSelect($index, $selectedCats) {
        $cats = $this->categories->getArray();
        $selectedCat = '';
        if (isset($selectedCats[$index])) {
            $selectedCat = $selectedCats[$index];
        }
        $select = Template::fromFile('view/select.html');
        $select->assign('index', $index);
        foreach ($cats as $index => $category) {
            if ($category == $selectedCat) {
                $option = $select->addSubtemplate('selectedOption');
            } else {
                $option = $select->addSubtemplate('option');
            }
            $option->assign('category', $category);
        }
        return $select->result();
    }

    private function loadBookCats() {
        $this->bookCats = array();
        if ($this->bookId == 0) return;
        $q = 'select category from book_cat_rel where book_id="'.$this->bookId.'"';
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $this->bookCats[] = $row['category'];
        }
    }

    private function numberOfSelectableCategories() {
        $numCats = count($this->categories->getArray());
        $numSelCats = floor(log($numCats));
        if ($numSelCats < 1) return 1;
        return $numSelCats;
    }

}
?>
