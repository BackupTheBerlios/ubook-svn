<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'notification/Searches.php';

$searches = new Searches();

if (isset($_GET['activate']) && $_GET['activate']) {
    $searches->setUp();
}

if (isset($_GET['deactivate']) && $_GET['deactivate']) {
    $searches->dropTable();
}

header('Location: admin.php');

?>