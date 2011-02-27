<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'books/Book.php';
require_once 'books/UsersBooks.php';
require_once 'tools/Template.php';

class Mailer {

    /**
     * Sends an E-Mail to the offerer of the book.
     *
     * Known usages:
     * - add.php
     * - book.php - "Anfrage: "
     * - Cleaner.php - "Erneuern: "
     *
     * @param int $bookId database id of th book, this mail is about
     * @param string $subjectStart beginning of the subject, the title will
     *  follow
     * @param string $message some text for the user, greeting will be added
     * @param string $replyTo mail address for the Reply-To field (optional)
     * @return bool false on failure
     */
    public static function send($bookId, $subjectStart, $message, $replyTo = null) {
        include_once 'mysql_conn.php';
        $query = 'select mail, id, author, title, price, year, isbn,'
            . ' description, auth_key from books where id="' . $bookId . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) != 1)
            return false;
        $book = Book::fromMySql($result);

        $subject = $subjectStart . $book->get('title');

        $tmpl = Template::fromFile('view/mail.txt');
        $tmpl->assign('message', $message);
        $tmpl->assign('bookText', $book->asText());
        if ($replyTo == null || $replyTo == $book->get('mail')) {
            $subTmpl = $tmpl->addSubtemplate('editBook');
            $link = self::editLink($book->get('id'), $book->get('auth_key'));
            $subTmpl->assign('editLink', $link);
            $books = new UsersBooks($book->get('mail'));
            $subTmpl->assign('usersBooks', $books->toString());
        } else {
            $subTmpl = $tmpl->addSubtemplate('viewBook');
            $link = self::bookLink($book->get('id'));
            $subTmpl->assign('bookLink', $link);
        }
        $content = $tmpl->result();

        return self::mail($book->get('mail'), $subject, $content, $replyTo);
    }

    /**
     * Simply sends a mail from uBook.
     *
     * Currently known usages:
     *
     * - admin_mail.php - user (admin) defined message
     * - Searches.php - "Neues Angebot"
     * <pre>
     * Hallo!
     *
     * Es gibt ein neues Buchangebot:
     *  Autor: $author
     *  Titel: $title
     *
     * Mehr Informationen über das Angebot:
     * $bookLink
     *
     * Suche nach '$searchKey' beenden:
     * $deleteSearchLink
     * </pre>
     * - Searches.php - "Suche beendet"
     * <pre>
     * Hallo!
     *
     * Folgende Suche(n) wurde(n) beendet und können über den Link neu
     * eingetragen werden:
     *
     * $searchKey1
     * $saveSearchLink1
     *
     * $searchKey2
     * $saveSearchLink2
     * </pre>
     * - summary.php - "Deine Angebote"
     * <pre>
     * Hallo,
     * hier eine Zusammenfassung aller Bücher, die mit deiner E-Mailadresse angeboten werden.
     *
     * $author: $title
     * $editLink
     * </pre>
     * - Mailer.php - see {@link send()}
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @return bool false on failure
     */
    public static function mail($to, $subject, $content, $reply_to = null) {
        $header = 'From: "uBook" <noreply>' . "\n";
        if (isset($reply_to)) {
            $header .= 'Reply-To: ' . $reply_to . "\n";
        } else {
            $content .= "\n";
            $content .= 'Diese Nachricht wurde automatisch versandt. Bitte antworte nicht darauf.';
        }
        $header .= 'Content-Type: text/plain; charset=UTF-8' . "\n";
        $subject = '[ubook] ' . $subject;
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        return mail($to, $encodedSubject, $content, $header);
    }

    public static function bookLink($bookId) {
        require_once 'WEBDIR.php';
        return WEBDIR . 'book.php?id=' . $bookId;
    }

    public static function editLink($bookId, $authKey) {
        return self::bookLink($bookId) . '&key=' . $authKey;
    }

    /**
     * Checks for valid chars, but not for an address defined in RFC 2822.
     * @param $mailAddress address to check
     * @return boolean seems to be valid or not
     */
    private static function isValidAddress($mailAddress) {
        return (boolean) ereg('^[[:print:]]+$', $mailAddress);
    }

    /**
     * Returns a field of the $_POST array, if it doesn't contain invalid
     * characters.
     * @param string $index index in the $_GET or $_POST array
     * @return string mail address or null, if there is no valid address
     */
    public static function mailFromUser($index) {
        $mail = null;
        if (isset($_GET[$index])) {
            $mail = $_GET[$index];
        }
        if (isset($_POST[$index])) {
            $mail = $_POST[$index];
        }
        if (self::isValidAddress($mail)) {
            return $mail;
        }
        return null;
    }

}

?>
