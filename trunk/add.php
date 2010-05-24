<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/* anti spam */
if (isset($_POST['name']) && $_POST['name'] != '') exit;

require_once 'books/Book.php';
require_once 'isbn/Isbn.php';
require_once 'tools/KeyGenerator.php';
require_once 'tools/SelectableCategories.php';

include_once 'mysql_conn.php';

$selectableCategories = new SelectableCategories();

if (isset($_POST['author'])) {
    require_once 'tools/Mailer.php';
    $mail = Mailer::mailFromUser('author');
    if ($mail && strstr($mail,'@')) {
        $quotedAuthor =  trim($_POST['mail']);
        $quotedTitle = trim($_POST['title']);
        $isbn = Isbn::stringFromPost();
        $year = (int) trim($_POST['year']);
        $price = (float) str_replace(',', '.', $_POST['price']);
        $quotedDescription = $_POST['desc'];
        $key = KeyGenerator::genKey();
        $query = 'insert into books'
                . ' (author, title, year, price, isbn, description, mail, auth_key'
                . ', created,expires)'
                . ' values ('
                . '"' . $quotedAuthor . '"'
                . ', "' . $quotedTitle . '"'
                . ', "' . $year . '"'
                . ', "' . $price . '"'
                . ', "' . $isbn . '"'
                . ', "' . $quotedDescription . '"'
                . ', "' . $mail . '"'
                . ', "' . $key . '"'
                . ', now()'
                . ', date_add(now(), interval 45 day)'
                . ')';
        mysql_query($query);
        $book_id = mysql_insert_id();
        $selectableCategories->setBookId($book_id);
        $selectableCategories->update();
        $subject = '';
        $message = 'Mit deiner E-Mailadresse wurde das unten stehende Buch angeboten. Hebe diese E-Mail auf, um das Angebot später ändern und löschen zu können.';
        require_once 'tools/Mailer.php';
        Mailer::send($book_id, $subject, $message);
        require_once 'notification/Searches.php';
        $searches = new Searches();
        if ($searches->areActivated()) {
            $author = stripslashes($quotedAuthor);
            $title = stripslashes($quotedTitle);
            $description = stripslashes($quotedDescription);
            $searches->bookAdded($book_id, $author, $title, $description);
        }
        header('Location: book.php?id='.$book_id.'&key='.$key.'&new=1');
    }
}

require_once 'tools/Output.php';
require_once 'tools/Template.php';

$output = new Output();
$output->setExpires('43200');
$output->addNavigationLink('first', 'Erste', 'add.php');
$addForm = Template::fromFile('view/add_form.html');

if (isset($_POST['isbn'])) {
    require_once 'isbn/IsbnQuery.php';
    try {
        $isbn = new Isbn($_POST['isbn']);
        $book = IsbnQuery::query($isbn);
    } catch (Exception $ex) {
        // forget it
    }
    $output->setFocus('author');
} else {
    $output->unlinkMenuEntry('Buch anbieten');
    $output->setFocus('isbn');
}

if (!isset($book)) $book = new Book();

$book->assignHtmlToTemplate($addForm);

$categoryString = implode(' ', $selectableCategories->createSelectArray());
$addForm->assign('categories', $categoryString);
$addForm->assign('isbn', $book->get('isbn'));
$addForm->addSubtemplate('isbnSubmit');
$output->send($addForm->result());
?>