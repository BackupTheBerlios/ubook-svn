<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

include_once 'func_webdir.php';
include_once 'mysql_conn_only.php';
include_once 'func_mail.php';

/*
 * Checks for old entries.
 * Locks the table books to avoid repeated mail sending.
 */
function check_old() {
	mysql_query('lock table books write');
	$query = 'delete from books where expired < now()';
	mysql_query($query);
	$query = 'select id,auth_key,mail,author,title,price,description from books where expired is null and expires < now()';
	$result = mysql_query($query);
	while ($book = mysql_fetch_array($result)) {
		$subject = 'Warnung: ';
		$message = 'Anscheinend hat sich in letzter Zeit niemand für dein unten stehendes Buch interessiert. In zehn Tagen wird das Angebot automatisch gelöscht. Um das zu verhindern, kannst du mit dem folgenden Link das Angebot erneuern:'."\n";
		$message .= webdir().'renew.php?id='.$book['id'].'&key='.$book['auth_key'];
		bookmail($book,$subject,$message);
		$query = 'update books set expired = date_add(now(), interval 10 day) where id="'.$book['id'].'"';
		mysql_query($query);
	}
	mysql_query('unlock tables');
}

check_old();
?>