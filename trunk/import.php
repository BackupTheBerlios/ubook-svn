<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'magic_quotes.php';
require_once 'books/Book.php';
require_once 'tools/Output.php';
require_once 'tools/Template.php';
require_once 'tools/SelectableCategories.php';

function import_book($bookString, Template $tmpl) {
    $labels = array('Autor', 'Titel', 'Preis', 'Erscheinungsjahr', 'ISBN', 'Beschreibung');
    $indices = array('author', 'title', 'price', 'year', 'isbn', 'description');
    $bookString = trim($bookString);
    $bookLines = split("\n", $bookString, sizeof($labels));
    for ($i = 0; $i < sizeof($labels); $i++) {
        list($label, $value) = split(':', $bookLines[$i], 2);
        if (trim($label) != $labels[$i])
            $value = '';
        $tmpl->assign($indices[$i], trim($value));
    }
}

if (isset($_POST['book_data'])) {
    $tmpl = Template::fromFile('view/add_form.html');
    import_book($_POST['book_data'], $tmpl);
    if (isset($_POST['mail'])) {
        $tmpl->assign('mail', $_POST['mail']);
    }
    $selectableCategories = new SelectableCategories();
    $categoryString = implode(' ', $selectableCategories->createSelectArray());
    $tmpl->assign('categories', $categoryString);
} else {
    $tmpl = Template::fromFile('view/import.html');
    if (isset($_GET['mail'])) {
        $mailTmpl = $tmpl->addSubtemplate('mail');
        $mailTmpl->assign('mail', $_GET['mail']);
    }
}

$output = new Output();
$output->send($tmpl->result());
?>