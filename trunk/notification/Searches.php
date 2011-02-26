<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'tools/KeyGenerator.php';

include_once 'mysql_conn.php';

class Searches {

	public function areActivated() {
		$r = mysql_query('describe searches;');
		if ($r) return true;
		else return false;
	}

	public function setUp() {
		$query = 'CREATE TABLE `searches` ('
		. '  `search` varchar(255) NOT NULL,'
		. '  `mail` varchar(128) NOT NULL,'
		. '  `life_counter` bigint(20) NOT NULL,'
		. '  `auth_key` varchar(32) NOT NULL,'
		. '  PRIMARY KEY  (`search`,`mail`)'
		. ')';
		mysql_query($query);
	}

	public function dropTable() {
		mysql_query('drop table searches');
	}

	public function addSearch($search, $mail) {
		$key = KeyGenerator::genKey();
		$query = 'insert into searches'
		. ' (search, mail, life_counter, auth_key) values'
		. ' ("' . $search . '"'
		. ', "' . $mail. '"'
		. ', 100 + (select count(*) from books)'
		. ', "' . $key . '");';
		mysql_query($query);
	}

	public function deleteSearch($search, $mail, $authKey) {
		$query = 'delete from searches where'
		. ' search = "' . $search . '"'
		. ' and mail = "' . $mail . '"'
		. ' and auth_key = "' . $authKey . '";';
		mysql_query($query);
	}

	public function bookAdded($id, $author, $title, $description) {
		require_once 'tools/Mailer.php';
		$this->informAbout($id, $author, $title, $description);
		$this->clean();
	}

	private function informAbout($id, $author, $title, $description) {
        $subject = 'Neues Angebot';
        $fixMailContent = 'Hallo!'."\n"
        . "\n"
        . 'Es gibt ein neues Buchangebot:' . "\n"
        . ' Autor: ' . $author . "\n"
        . ' Titel: ' . $title . "\n"
        . "\n"
        . 'Mehr Informationen über das Angebot:' . "\n"
        . Mailer::bookLink($id) . "\n"
        . "\n";
        $informedMails = array();
        $bookText = $author . ' ' . $title . ' ' . $description;
        $query = 'select search, mail, life_counter, auth_key from searches;';
        $r = mysql_query($query);
        while ($search_array = mysql_fetch_array($r)) {
            if (array_search($search_array['mail'], $informedMails)) {
                // a mail has already been sent
                continue;
            }
            if (self::searchMatchesBook($search_array['search'], $bookText)) {
                $mailContent = $fixMailContent;
                if ($search_array['life_counter'] > 0) {
                    $mailContent .= 'Suche nach \''
                    . $search_array['search'] . '\' beenden:' . "\n"
                    . WEBDIR . 'delete_search.php?search=' . $search_array['search']
                    . '&mail=' . $search_array['mail']
                    . '&auth_key=' . $search_array['auth_key'] . "\n";
                }
                Mailer::mail($search_array['mail'], $subject, $mailContent);
                $informedMails[] = $search_array['mail'];
            }
        }
	}

	private function searchMatchesBook($search, $bookText) {
		$keys = explode(' ', $search);
		foreach ($keys as $i => $k) {
			if (!$k) continue;
			if (strpos($bookText, $k) === false) {
				return false;
			}
		}
		return true;
	}

	private function clean() {
        $subject = 'Suche beendet';
        $fixMailContent =  'Hallo!'."\n"
        . "\n"
        . 'Folgende Suche(n) wurde(n) beendet und können über den Link neu' . "\n"
        . 'eingetragen werden:' . "\n";

        $toClean = array();
        $query = 'select search, mail from searches where life_counter=0;';
        $r = mysql_query($query);
        while ($search_array = mysql_fetch_array($r)) {
        	$mail = $search_array['mail'];
        	if (!isset($toClean[$mail])) {
        		$toClean[$mail] = array();
        	}
        	$toClean[$mail][] = $search_array['search'];
        }
        $query = 'delete from searches where life_counter <= 0;';
        mysql_query($query);
        $query = 'update searches set life_counter = life_counter - 1;';
        mysql_query($query);
        foreach ($toClean as $mail => $searchArray) {
            $mailContent = $fixMailContent;
            foreach ($searchArray as $i => $search) {
            	$mailContent .= "\n"
            	. $search . "\n"
                . WEBDIR . 'save_search.php?search=' . urlencode($search)
                . '&mail=' . urlencode($mail) . "\n";
            }
            Mailer::mail($mail, $subject, $mailContent);
        }
	}

}
?>
