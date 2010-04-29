<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/**
 * Defines the constant WEBDIR. Normally it consists of the server name and
 * directory. Executed locally, it will be a local file path.
 */
function define_webdir() {
    if (isset($_SERVER['SERVER_NAME'])) {
        $protocoll = 'http://';
        $uri = $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
    } else {
        $protocoll = 'file://';
        $uri = $_ENV['PWD'] . '/';
    }
    define('WEBDIR', $protocoll . str_replace('//', '/', $uri));
}

define_webdir();

?>
