<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'notification/Searches.php';

if (!isset($_GET['search'])) header('Location: ./');
if (!isset($_GET['mail'])) header('Location: ./');
if (!isset($_GET['auth_key'])) header('Location: ./');

$searches = new Searches();
$searches->deleteSearch($_GET['search'], $_GET['mail'], $_GET['auth_key']);
header('Location: ./?search=' . urlencode($_GET['search']) . '&searchDeleted=1');

?>
