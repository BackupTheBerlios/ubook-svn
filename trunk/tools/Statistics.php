<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
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
        $tmpl->assign('total', (int) $s[2]);
        $tmpl->assign('offerors', $s[3]);
        $booksPerOfferor = $s[3] ? round($s[1] / $s[3], 1) : 0;
        $tmpl->assign('booksPerOfferor', $booksPerOfferor);
        $tmpl->assign('images', $s[4]);
        $imageFraction = $s[1] ? round($s[4] / $s[1] * 100) : 0;
        $tmpl->assign('imageFraction', $imageFraction);
        $imageFiles = array();
        $iterator = new DirectoryIterator(self::STATS_DIR);
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $iterator->next();
            if (substr($entry, -4) == '.png') {
                $imageFiles[] = $entry;
            }
        }
        sort($imageFiles);
        foreach ($imageFiles as $file) {
            $sub = $tmpl->addSubtemplate('image');
            $sub->assign('url', self::STATS_DIR . $file);
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
