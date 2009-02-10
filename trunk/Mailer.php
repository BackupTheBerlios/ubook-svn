<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

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
		include_once 'func_book.php';
		$query = 'select mail, id, author, title, price, year, description, auth_key from books where id="'.$book_id.'"';
		$result = mysql_query($query);
		if (mysql_num_rows($result) != 1) return false;
		$book = fetch_book($result);

		$subject = $subject.$book['title'];

		$content = 'Hallo!'."\n";
		$content .= "\n";
		$content .= $message."\n\n";
		$content .= ' Autor: '.$book['author']."\n";
		$content .= ' Titel: '.$book['title']."\n";
		$content .= ' Preis: '.str_replace('.',',',$book['price']).' Euro'."\n";
		$content .= ' Erscheinungsjahr: '.$book['year']."\n";
		$content .= ' Beschreibung:'."\n";
		$content .= $book['description']."\n\n";

		if ($reply_to == null || $reply_to == $book['mail']) {
			$content .= 'Buchangebot ändern oder löschen:'."\n";
			$content .= Mailer::edit_link($book['id'],$book['auth_key'])."\n";
		}
		else {
			$content .= 'Buchangebot ansehen:'."\n";
			$content .= Mailer::book_link($book['id'],$book['auth_key'])."\n";
		}

		return Mailer::mail($book['mail'], $subject, $content, $reply_to);
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
		$subject = utf8_decode($subject);
		return mail($to, $subject, $content, $header);
	}

	function book_link($book_id) {
		require_once 'WEBDIR.php';
		return WEBDIR.'book.php?id='.$book_id;
	}

	function edit_link($book_id, $auth_key) {
		return self::book_link($book_id).'&key='.$auth_key;
	}

}

?>
