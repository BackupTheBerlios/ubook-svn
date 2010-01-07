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
interface Isbn {

	public function getOriginal();

	public function getSegmented();

	public function getNumber();

	public function isValid();

	public function isIsbn10();

	public function isIsbn13();

}

?>