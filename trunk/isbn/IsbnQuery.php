<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'net/HttpUrl.php';
require_once 'net/ThreadedDownloader.php';
require_once 'IsbnDbDotComProvider.php';
require_once 'UBKarlsruheProvider.php';

/**
 * Fetches information about a book from different APIs.
 * @author maikel
 */
class IsbnQuery {

	public static function query($isbn) {
        $ubka = new UBKarlsruheProvider();
        ThreadedDownloader::startDownload($ubka->urlFor($isbn), $ubka);
        $iddc = new IsbnDbDotComProvider();
        ThreadedDownloader::startDownload($iddc->urlFor($isbn), $iddc);
        ThreadedDownloader::finishAll();
        if ($ubka->getBook()) {
            $book = $ubka->getBook();
            if (sizeof($book) > 0) {
                return $book;
            }
        }
        if ($iddc->getBook()) {
            $book = $iddc->getBook();
            if (sizeof($book) > 0) {
                return $book;
            }
        }
		return array('isbn' => $isbn);
	}

}

?>