<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
*/

require_once 'mysql_conn.php';
require_once 'tools/BookFetcher.php';
require_once 'tools/Parser.php';
require_once 'tools/Image.php';

/*
 * Checks POST data and sends E-Mail, if everything is correct.
*/
function send($book) {
    /*
	 * $_POST['name'] should contain a mail address.
	 * It is named 'name' to trick robots.
    */
    if (!isset($_POST['name'])) return false;
    $user_mail = stripslashes($_POST['name']);
    if (!strstr($user_mail,'@')) return true;
    require_once 'tools/Mailer.php';
    $subject = 'Anfrage: ';
    $message = 'Es hat jemand mit der E-Mailadresse "'.$user_mail.'" Interesse für das unten stehende Buch bekundet.';
    if (isset($_POST['user_text']) && $_POST['user_text']) {
        $message .= ' Folgende Nachricht wurde mitgesandt:'."\n\n";
        $message .= stripslashes($_POST['user_text'])."\n";
    }
    $booked = Mailer::send($book['id'],$subject,$message,$user_mail);
    header('Location: book.php?id='.$book['id'].'&booked='.$booked);
    exit;
}


if (!isset($_GET['id'])) exit;
$book_id = (int) $_GET['id'];

/*** begin computing output ***/
require_once 'tools/Output.php';
require_once 'tools/Template.php';

$tmpl = Template::fromFile('view/book.html');

$tmpl->assign('id', $book_id);

$result = mysql_query('select id,author,title,year,price,description,auth_key,mail from books where id="'.$book_id.'"');
if (mysql_num_rows($result) != 1) {
    $tmpl->addSubtemplate('messageNotFound');
    $output = new Output($tmpl->result());
    $output->sendNotFound();
    exit;
}

$book = BookFetcher::fetch($result);

/* checks mail sending, no returning on success */
$mail_error = send($book);

$bookTmpl = $tmpl->addSubtemplate('book');

if ($mail_error) {
    $tmpl->addSubtemplate('bookingFailed');
}

if (isset($_GET['new'])) {
    $tmpl->addSubtemplate('messageNew');
}

if (isset($_GET['renew'])) {
    if ($_GET['renew']) {
        $tmpl->addSubtemplate('messageRenewed');
    } else {
        $tmpl->addSubtemplate('messageNotRenewed');
    }
}

if (isset($_GET['uploaded'])) {
    $tmpl->addSubtemplate('messageUploaded');
}

$tmpl->assign('img_tag', Image::imgTag($book_id));

Parser::htmlbook($book);
foreach ($book as $name => $value) {
    $bookTmpl->assign($name, $value);
}
$bookTmpl->assign('nl2br_description', nl2br($book['description']));

$categoryArray = array();
$result = mysql_query('select category from book_cat_rel where book_id="'.$book_id.'"');
while ($row = mysql_fetch_array($result)) {
    $categoryArray[] = $row['category'];
}
$bookTmpl->assign('category_string', implode(', ', $categoryArray));

if (isset($_GET['key'])) {
    $bookTmpl->assign('id', $_GET['id']);
    $bookTmpl->assign('key', $_GET['key']);
    $tmpl->addSubtemplate('editAndDelete');
    if (Image::uploadable()) {
        $tmpl->addSubtemplate('uploadButton');
    }
} else {
    $showBookForm = true;
    if (isset($_GET['booked'])) {
        if ($_GET['booked']) {
            $tmpl->addSubtemplate('booked');
            $showBookForm = false;
        } else {
            $tmpl->addSubtemplate('bookingFailed');
        }
    }
    if ($showBookForm) {
        $form = $tmpl->addSubtemplate('bookingForm');
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
    $tmpl->addSubtemplate('personLink');
}

$output = new Output($tmpl->result());
$output->send();
?>