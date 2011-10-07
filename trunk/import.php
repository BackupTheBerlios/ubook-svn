<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'magic_quotes.php';
require_once 'books/Book.php';
require_once 'tools/Mailer.php';
require_once 'tools/Output.php';
require_once 'tools/Parser.php';
require_once 'text/Template.php';
require_once 'tools/SelectableCategories.php';

function import_book($bookString, Template $tmpl) {
    $labels = array('Autor', 'Titel', 'Preis', 'Erscheinungsjahr', 'ISBN', 'Beschreibung');
    $indices = array('author', 'title', 'price', 'year', 'isbn', 'description');
    $bookString = trim($bookString);
    $bookLines = split("\n", $bookString, sizeof($labels));
    for ($i = 0; $i < sizeof($labels); $i++) {
        list($label, $value) = split(':', $bookLines[$i], 2);
        if (trim($label) != $labels[$i]) {
            $value = '';
        }
        $value = Parser::text2html(stripslashes(trim($value)));
        $tmpl->assign($indices[$i], $value);
    }
}

$usermail = Parser::text2html(stripslashes(Mailer::mailFromUser('mail')));
if (isset($_POST['book_data'])) {
    $tmpl = Template::fromFile('view/add_form.html');
    import_book($_POST['book_data'], $tmpl);
    if (isset($_POST['mail'])) {
        $tmpl->assign('mail', $usermail);
    }
    $selectableCategories = new SelectableCategories();
    $categoryString = implode(' ', $selectableCategories->createSelectArray());
    $tmpl->assign('categories', $categoryString);
} else {
    $tmpl = Template::fromFile('view/import.html');
    if (isset($_GET['mail'])) {
        $mailTmpl = $tmpl->addSubtemplate('mail');
        $mailTmpl->assign('mail', $usermail);
    }
}

$output = new Output();
$output->send($tmpl->result());
?>