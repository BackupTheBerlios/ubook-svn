<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'books/Book.php';

class Mailer {

    /**
     * public
     * Sends an E-Mail to the offerer of the book.
     *
     * @param int $book_id
     * @param string $subject
     * @param string $message
     * @param string $reply_to is optional
     * @return bool false on failure
     */
    function send($book_id, $subject, $message, $reply_to = null) {
        include_once 'mysql_conn.php';
        $query = 'select mail, id, author, title, price, year, description, auth_key from books where id="'.$book_id.'"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) != 1) return false;
        $book = Book::fromMySql($result);

        $subject = $subject . $book->get('title');

        $content = 'Hallo!'."\n";
        $content .= "\n";
        $content .= $message."\n\n";
        $content .= $book->asText() . "\n\n";

        if ($reply_to == null || $reply_to == $book->get('mail')) {
            $content .= 'Buchangebot ändern oder löschen:'."\n";
            $content .= Mailer::edit_link($book->get('id'),$book->get('auth_key'))."\n";
        }
        else {
            $content .= 'Buchangebot ansehen:'."\n";
            $content .= Mailer::book_link($book->get('id'))."\n";
        }

        return Mailer::mail($book->get('mail'), $subject, $content, $reply_to);
    }

    /**
     * public
     * Simply sends an mail from uBook, without further content.
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @return bool false on failure
     */
    function mail($to, $subject, $content, $reply_to = null) {
        $header = 'From: "uBook" <noreply>'."\n";
        if (isset($reply_to)) {
            $header .= 'Reply-To: '.$reply_to."\n";
        }
        else {
            $content .= "\n";
            $content .= 'Diese Nachricht wurde automatisch versandt. Bitte antworte nicht darauf.';
        }
        $header .= 'Content-Type: text/plain; charset=UTF-8'."\n";
        $subject = '[ubook] '.$subject;
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject).'?=';
        $subject = utf8_decode($subject);
        return mail($to, $encodedSubject, $content, $header);
    }

    function book_link($book_id) {
        require_once 'WEBDIR.php';
        return WEBDIR.'book.php?id='.$book_id;
    }

    function edit_link($book_id, $auth_key) {
        // self does not work with PHP4
        return Mailer::book_link($book_id).'&key='.$auth_key;
    }

    /**
     * Checks for valid chars, but not for an address defined in RFC 2822.
     * @param $mailAddress address to check
     * @return boolean seems to be valid or not
     */
    function isValidAddress($mailAddress) {
        return (boolean) ereg('^[[:print:]]+$', $mailAddress);
    }

    function mailFromUser($postIndex) {
        if (!isset($_POST[$postIndex])) return null;
        $mail = $_POST[$postIndex];
        if (Mailer::isValidAddress($mail)) {
            return $mail;
        }
        return null;
    }

}

?>
