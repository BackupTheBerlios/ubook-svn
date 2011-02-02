<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'books/SearchKey.php';
require_once 'notification/Searches.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) {
    header('Location: ./');
}

$mail = null;

if (isset($_GET['mail'])) {
    $mail = $_GET['mail'];
}
if (isset($_GET['name'])) { // field for mail address
    $mail = $_GET['name'];
}

if ($mail) {
    $searches = new Searches();
    $searches->addSearch($searchKey->asText(), $mail);
    header('Location: ./?search=' . urlencode($searchKey->asText()) . '&searchSaved=1');
}

$tmpl = Template::fromFile("view/save_search.html");
$tmpl->assign('searchKey', $searchKey->asHtml());
$tmpl->assign('urlSearchKey', urlencode($searchKey->asHtml()));

$output = new Output();
$output->send($tmpl->result());
?>
