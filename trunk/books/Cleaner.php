<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'tools/Image.php';
require_once 'tools/Mailer.php';
require_once 'tools/WEBDIR.php';

include_once 'mysql_conn.php';

/**
 * Deletes old books.
 * @author maikel
 */
abstract class Cleaner {

    /**
     * Checks for old entries.
     * Locks the table books to avoid repeated mail sending.
     */
    public static function checkOld() {
        if (!self::tryToGetLock())
            return;
        /* now we have the lock */
        self::deleteExpired();
        self::checkExpire();
        self::releaseLock();
    }

    private static function tryToGetLock() {
        /* checking if another thread holds the lock */
        $is_free_result = mysql_query('select is_free_lock("check_old")');
        $is_free_row = mysql_fetch_row($is_free_result);
        if ($is_free_row[0] == 0)
            return false;
        /* trying to get the lock */
        $get_lock_result = mysql_query('select get_lock("check_old", 600)');
        $get_lock_row = mysql_fetch_row($get_lock_result);
        if ($get_lock_row[0])
            return true;
        else
            return false;
    }

    private static function releaseLock() {
        mysql_query('select release_lock("check_old")');
    }

    private static function deleteExpired() {
        if (is_writable(Image::PATH)) {
            $query = 'select id from books where expired < now()';
            $result = mysql_query($query);
            while ($row = mysql_fetch_row($result)) {
                $img = new Image($row[0]);
                $img->delete();
            }
        }
        $query = 'delete from books where expired < now()';
        mysql_query($query);
    }

    private static function checkExpire() {
        $query = 'select id, auth_key, mail, author, title, price, description'
                . ' from books where expired is null and expires < now()';
        $result = mysql_query($query);
        while ($book = mysql_fetch_array($result)) {
            $subject = 'Erneuern: ';
            $message = 'Anscheinend hat sich in letzter Zeit niemand für dein'
                    . ' unten stehendes Buch interessiert. In zehn Tagen wird das'
                    . ' Angebot automatisch gelöscht. Um das zu verhindern, kannst du'
                    . ' mit dem folgenden Link das Angebot erneuern:' . "\n"
                    . WEBDIR . 'renew.php?id=' . $book['id'] . '&key=' . $book['auth_key']
                    . "\n\n"
                    . 'Falls das Buch bereits gelöscht wurde dann benutze diesen Link,'
                    . ' um es einfach wieder zu importieren:' . "\n"
                    . WEBDIR . 'import.php?mail=' . $book['mail'];
            Mailer::send($book['id'], $subject, $message);
            $query = 'update books set expired = date_add(now(), interval 10 day)'
                    . ' where id="' . $book['id'] . '"';
            mysql_query($query);
        }
    }

}

?>