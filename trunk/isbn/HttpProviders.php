<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'BooklookerProvider.php';
require_once 'GoogleProvider.php';
require_once 'IsbnDbDotComProvider.php';
require_once 'UBKarlsruheProvider.php';

/**
 * Creates all available IsbnDbProviders.
 * @author maikel
 */
class HttpProviders {

	public static function createProviders() {
        $list = array();
        $list[] = new GoogleProvider();
        $list[] = new UBKarlsruheProvider();
        //$list[] = new BooklookerProvider('key needed');
        //$list[] = new IsbnDbDotComProvider('key needed');
        return $list;
	}

}

?>