<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'net/DownloadThread.php';

/**
 * Provides information about an ISBN database.
 * @author maikel
 */
interface IsbnDbProvider {

    /**
     * Tries to get information about a book with the given ISBN.
     *
     * @param Isbn $isbn to search for
     * @return Book containing found data
     */
    public static function query(Isbn $isbn);
}

?>