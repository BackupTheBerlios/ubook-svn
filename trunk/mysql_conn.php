<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

/*
 * This script connects to the database or exits by sending an error message.
 * If a connection is etablished, old database entries will be deleted.
 */

require_once 'tools/MyDatabase.php';

MyDatabase::connect();

if (!MyDatabase::getConnection()) {
    require 'header.php';
    ?>
    <h2>Störung</h2>
    <div class="text">
    Leider besteht zur Zeit keine Verbindung zur Datenbank. :-(
    </div>
    <?php
    require 'footer.php';
    exit;
}
?>