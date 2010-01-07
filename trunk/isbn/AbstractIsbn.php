<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

/**
 * TODO: Depricated.
 * Provides functions of all ISBNs.
 * @author maikel
 */
abstract class AbstractIsbn {

	private $original;
	private $segmented;
	private $number;

	public function __construct($originalString) {
		$this->original = $originalString;
        if (preg_match('/^[0-9-]+$/', $originalString) == 0) {
        	return;
        }
		if (strpos($originalString, '-') === false) {
			$this->segmented = null;
		} else {
			$this->segmented = $originalString;
		}
		$this->number = str_replace('-', '', $originalString);
	}

	public function getOriginal() {
		return $this->original;
	}

	public function getSegmented() {
		return $this->segmented;
	}

	public function getNumber() {
		return $this->number;
	}

}

?>