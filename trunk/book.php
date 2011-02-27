<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'mysql_conn.php';
require_once 'books/Book.php';
require_once 'tools/Parser.php';
require_once 'tools/Image.php';
require_once 'tools/Output.php';
require_once 'tools/Template.php';

/**
 * Structures the display of a book.
 */
class bookPage {

    private $bookId;
    private $tmpl;
    private $output;
    private $book;

    public function __construct($bookId, $output) {
        $this->bookId = $bookId;
        $this->output = $output;
        $this->tmpl = Template::fromFile('view/book.html');
        $this->book = $this->selectBook($bookId);
        $this->tmpl->assign('id', $bookId);
    }

    /**
     * Checks POST data and sends E-Mail, if everything is correct.
     * @return bool true, if mail variable doesn't contain an @.
     */
    public function sendMailIfRequested() {
        /*
	     * $_POST['name'] should contain a mail address.
	     * It is named 'name' to trick robots.
        */
        if (!isset($_POST['name'])) return false;
        $user_mail = stripslashes(Mailer::mailFromUser('name'));
        if (!strstr($user_mail,'@')) return true;
        require_once 'tools/Mailer.php';
        $subject = 'Anfrage: ';
        $message = 'Es hat jemand mit der E-Mailadresse "'.$user_mail.'" Interesse für das unten stehende Buch bekundet.';
        if (isset($_POST['user_text']) && $_POST['user_text']) {
            $message .= ' Folgende Nachricht wurde mitgesandt:'."\n\n";
            $message .= stripslashes($_POST['user_text'])."\n";
        }
        $booked = Mailer::send($this->bookId,$subject,$message,$user_mail);
        header('Location: book.php?id='.$this->bookId.'&booked='.$booked);
        exit;
    }

    public function showBook() {
        $bookTmpl = $this->tmpl->addSubtemplate('book');
        $bookTmpl->assign('img_tag', Image::imgTag($this->bookId));
        $this->book->assignHtmlToTemplate($bookTmpl);
        $desc = nl2br(Parser::text2html($this->book->get('description')));
        $bookTmpl->assign('nl2br_description', $desc);
        $categoryArray = array();
        $result = mysql_query('select category from book_cat_rel where'
                . ' book_id="' . $this->bookId . '"');
        while ($row = mysql_fetch_array($result)) {
            $categoryArray[] = $row['category'];
        }
        $bookTmpl->assign('category_string', implode(', ', $categoryArray));
    }

    public function showNormalElements() {
        $showBookForm = true;
        if (isset($_GET['booked'])) {
            if ($_GET['booked']) {
                $this->tmpl->addSubtemplate('booked');
                $showBookForm = false;
            } else {
                $this->tmpl->addSubtemplate('bookingFailed');
            }
        }
        if ($showBookForm) {
            $form = $this->tmpl->addSubtemplate('bookingForm');
            if (isset($_POST['name'])) {
                $form->assign('user_mail', $_POST['name']);
            } else {
                $form->assign('user_mail', '');
            }
            if (isset($_POST['user_text'])) {
                $form->assign('user_text', $_POST['user_text']);
            } else {
                $form->assign('user_text', '');
            }
        }
        $this->tmpl->addSubtemplate('personLink');
    }

    public function showAdminElements($userKey) {
        $this->tmpl->assign('key', $_GET['key']);
        $this->tmpl->addSubtemplate('editAndDelete');
        if (Image::exists($this->bookId)) {
            $this->tmpl->addSubtemplate('imgDeleteButton');
        }
        elseif (Image::uploadable()) {
            $this->tmpl->addSubtemplate('imgUploadButton');
        }
        if (isset($_GET['new'])) {
            $this->tmpl->addSubtemplate('messageNew');
        }
        if (isset($_GET['renew'])) {
            if ($_GET['renew']) {
                $this->tmpl->addSubtemplate('messageRenewed');
            } else {
                $this->tmpl->addSubtemplate('messageNotRenewed');
            }
        }
        if (isset($_GET['uploaded'])) {
            $this->tmpl->addSubtemplate('messageUploaded');
        }
    }

    public function send() {
        $this->output->send($this->tmpl->result());
    }

    private function selectBook($bookId) {
        $result = mysql_query('select id, author, title, year, price, isbn,'
                . ' description, auth_key, mail from books where id="'
                . $bookId . '"');
        if (mysql_num_rows($result) != 1) {
            $this->tmpl->addSubtemplate('messageNotFound');
            $this->output->sendNotFound($this->tmpl->result());
            exit;
        }
        return Book::fromMySql($result);
    }
}


$output = new Output();

if (!isset($_GET['id'])) {
    $output->sendNotFound();
    exit;
}

$page = new bookPage((int) $_GET['id'], $output);

/* checks mail sending, no returning on success */
$page->sendMailIfRequested();

$page->showBook();

if (isset($_GET['key'])) {
    $page->showAdminElements($_GET['key']);
} else {
    $page->showNormalElements();
}

$page->send();
?>