<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

/* anti spam */
if (sizeof($_POST) && (!isset($_POST['name']) || $_POST['name'] != '')) {
    exit;
}

require_once 'books/Book.php';
require_once 'isbn/Isbn.php';
require_once 'isbn/IsbnQuery.php';
require_once 'notification/Searches.php';
require_once 'tools/KeyGenerator.php';
require_once 'tools/Mailer.php';
require_once 'tools/Output.php';
require_once 'tools/SelectableCategories.php';
require_once 'tools/Template.php';

include_once 'mysql_conn.php';

class addPage {

    private static $formFields = array('name', 'author', 'mail', 'title',
        'isbn', 'year', 'price', 'desc', 'categories');
    private $selectableCategories;
    private $categoryString;
    private $mail;
    private $book;
    private $output;
    private $addForm;

    public function __construct() {
        $this->selectableCategories = new SelectableCategories();
        $this->output = new Output();
        $this->output->setExpires('43200');
        $this->output->addNavigationLink('first', 'Erste', 'add.php');
        $this->addForm = Template::fromFile('view/add_form.html');
    }

    public function formSubmitted() {
        if (sizeof($_POST) != sizeof(self::$formFields)) {
            return false;
        }
        foreach (self::$formFields as $f) {
            if (!isset($_POST[$f])) {
                return false;
            }
        }
        return true;
    }

    public function formDataComplete() {
        $mail = $this->getMail();
        return ($mail && strstr($mail, '@'));
    }

    public function InsertAndSendMail() {
        $quotedAuthor = trim($_POST['mail']);
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
                . ', "' . $this->getMail() . '"'
                . ', "' . $key . '"'
                . ', now()'
                . ', date_add(now(), interval 45 day)'
                . ')';
        mysql_query($query);
        $book_id = mysql_insert_id();
        $this->selectableCategories->setBookId($book_id);
        $this->selectableCategories->update();
        $subject = '';
        $message = 'Mit deiner E-Mailadresse wurde das unten stehende Buch angeboten. Hebe diese E-Mail auf, um das Angebot später ändern und löschen zu können.';
        Mailer::send($book_id, $subject, $message);
        $searches = new Searches();
        if ($searches->areActivated()) {
            $author = stripslashes($quotedAuthor);
            $title = stripslashes($quotedTitle);
            $description = stripslashes($quotedDescription);
            $searches->bookAdded($book_id, $author, $title, $description);
        }
        header('Location: book.php?id=' . $book_id . '&key=' . $key . '&new=1');
        exit;
    }

    public function fillFormAndMarkWrongMail() {
        $this->fillForm();
        $this->addForm->addSubtemplate('wrongMail');
    }

    public function checkIsbnQuery() {
        if (!$this->book && isset($_POST['isbnQuery']) && isset($_POST['isbn'])) {
            try {
                $isbn = new Isbn($_POST['isbn']);
                $this->book = IsbnQuery::query($isbn);
                $this->output->setFocus('author');
            } catch (Exception $ex) {
                $this->fillForm();
                $this->addForm->addSubtemplate('wrongIsbn');
            }
        } else {
            $this->output->unlinkMenuEntry('Buch anbieten');
            $this->output->setFocus('isbn');
        }
    }

    public function display() {
        if ($this->book == null) {
            $book = new Book();
        } else {
            $book = $this->book;
        }

        $book->assignHtmlToTemplate($this->addForm);

        $this->addForm->assign('categories', $this->getCategoryString());
        $this->addForm->assign('isbn', $book->get('isbn'));
        $this->addForm->addSubtemplate('isbnSubmit');
        $this->output->send($this->addForm->result());
    }

    private function fillForm() {
        $bookData = array();
        foreach (self::$formFields as $f) {
            $bookData[$f] = stripslashes($_POST[$f]);
        }
        $mail = $bookData['author'];
        $bookData['author'] = $bookData['mail'];
        $bookData['mail'] = $mail;
        $bookData['description'] = $bookData['desc'];
        assert("is_array(\$_POST['categories'])");
        $this->getCategoryString($_POST['categories']);
        $this->book = new Book($bookData);
    }

    private function getMail() {
        if ($this->mail === null) {
            $this->mail = Mailer::mailFromUser('author');
        }
        return $this->mail;
    }

    private function getCategoryString($selectedCats = null) {
        if ($this->categoryString == null) {
            $selectArr = $this->selectableCategories->
                            createSelectArray($selectedCats);
            $this->categoryString = implode(' ', $selectArr);
        }
        return $this->categoryString;
    }

}

$page = new addPage();

if ($page->formSubmitted()) {
    if ($page->formDataComplete()) {
        $page->InsertAndSendMail();
    } else {
        $page->fillFormAndMarkWrongMail();
    }
}

$page->checkIsbnQuery();

$page->display();
?>