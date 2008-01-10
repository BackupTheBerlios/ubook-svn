<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

include_once 'func_webdir.php';

function ubookmail($to,$subject,$content) {
	$header = 'From: "uBook" <noreply>'."\n";
	$header .= 'Content-Type: text/plain; charset=UTF-8'."\n";
	$subject = '[ubook] '.$subject;
	$subject = utf8_decode($subject);
	$content .= "\n";
	$content .= 'Diese Nachricht wurde automatisch versandt. Bitte antworte nicht darauf.';
	return mail($to, $subject, $content, $header);
}

function edit_link($book_id, $auth_key) {
	return webdir().'book.php?id='.$book_id.'&key='.$auth_key;
}

function bookmail($book,$subject,$message) {
	include_once 'mysql_conn.php';
	include_once 'func_book.php';
	$query = 'select mail, id, author, title, price, year, description, auth_key from books where id="'.$book['id'].'"';
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
	$content .= 'Buchangebot ändern oder löschen:'."\n";
	$content .= edit_link($book['id'],$book['auth_key'])."\n";

	return ubookmail($book['mail'],$subject,$content);
}

?>
