<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'Providers.php';

require_once 'net/HttpUrl.php';
require_once 'net/ThreadedDownloader.php';

/**
 * Fetches information about a book from different APIs.
 * @author maikel
 */
class IsbnQuery {

    /**
     * Proofs, if an ISBN string contains only valid chars.
     * @param string $isbn to check
     * @return boolean true, if $isbn contains only valid chars
     */
    public static function containsValidChars($isbn) {
        if (preg_match('/^[0-9-]+[xX]?$/', $isbn) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function query($isbn) {
        $providers = Providers::createProviders();
        foreach ($providers as $i => $p) {
            ThreadedDownloader::startDownload($p->urlFor($isbn), $p);
        }
        ThreadedDownloader::finishAll();
        foreach ($providers as $i => $p) {
            $book = $p->getBook();
            if ($book && sizeof($book) > 0) {
                return $book;
            }
        }
        return array('isbn' => $isbn);
    }

}


?>