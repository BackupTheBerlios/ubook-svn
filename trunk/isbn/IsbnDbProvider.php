<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'net/ThreadedDownloader.php';

/**
 * Provides information about an ISBN database.
 * @author maikel
 */
interface IsbnDbProvider extends Processor {
    function urlFor(Isbn $isbn);
}

?>