<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Image.php';
require_once 'text/Template.php';
require_once 'mysql_conn.php';

/**
 * Provides some basic numbers.
 */
class Statistics {
    const STATS_DIR = 'stats/';
    const STATS_FILE = 'stats/statistics.log';
    const BOOKS = 'select count(*) from books;';
    const IDS = 'select max(id) from books;';
    const MAIL_ADRESSES = 'select count(distinct mail) from books;';

    private $stats = array();

    public function __construct() {
        $this->loadStats();
    }

    public function writeStats() {
        if (file_exists(self::STATS_FILE)) {
            if (!is_writable(self::STATS_FILE)) {
                return;
            }
            if (date('Ymd') == date('Ymd', filemtime(self::STATS_FILE))) {
                return;
            }
        } else {
            if (!is_writable(self::STATS_DIR)) {
                return;
            }
        }
        $data = $this->appendString();
        $handle = fopen(self::STATS_FILE, 'a');
        fwrite($handle, $data);
        fclose($handle);
    }

    public function fillTemplate(Template $tmpl) {
        $s = &$this->stats;
        $tmpl->assign('statsFile', self::STATS_FILE);
        $tmpl->assign('books', $s[1]);
        $tmpl->assign('total', $s[2]);
        $tmpl->assign('offerors', $s[3]);
        $tmpl->assign('booksPerOfferor', round($s[1] / $s[3], 1));
        $tmpl->assign('images', $s[4]);
        $tmpl->assign('imageFraction', round($s[4] / $s[1] * 100));
        $iterator = new DirectoryIterator(self::STATS_DIR);
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $iterator->next();
            if (substr($entry, -4) == '.png') {
                $sub = $tmpl->addSubtemplate('image');
                $sub->assign('url', self::STATS_DIR . $entry);
            }
        }
    }

    private function loadStats() {
        $s = &$this->stats;
        $s[] = date('Y-m-d H:i:s');
        $result = mysql_query(self::BOOKS);
        $s[] = current(mysql_fetch_row($result));
        $result = mysql_query(self::IDS);
        $s[] = current(mysql_fetch_row($result));
        $result = mysql_query(self::MAIL_ADRESSES);
        $s[] = current(mysql_fetch_row($result));
        $images = 0;
        $iterator = new DirectoryIterator(Image::PATH);
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $iterator->next();
            if (substr($entry, -4) != '.png') {
                continue;
            }
            if (substr($entry, -10) == '_thumb.png') {
                continue;
            }
            $images++;
        }
        $s[] = $images;
    }

    private function appendString() {
        $s = implode("\t", $this->stats) . "\n";
        if (!file_exists(self::STATS_FILE)) {
            $s = '#   date' . "\t" . 'time' . "\t" . 'books' . "\t" . 'ids'
                    . "\t" . 'mails' . "\t" . 'images' . "\n" . $s;
        }
        return $s;
    }

}

?>
