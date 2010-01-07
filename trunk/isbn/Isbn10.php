<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractIsbn.php';

/**
 * TODO: Depricated.
 * Implements an ISBN10.
 * @author maikel
 */
class Isbn10 extends AbstractIsbn {

	public function __construct($originalString) {
		parent::__construct($originalString);
	}

	public function isValid() {
		return false;
	}

    public function isIsbn10() {
    	return true;
    }

    public function isIsbn13() {
    	return false;
    }


}

?>