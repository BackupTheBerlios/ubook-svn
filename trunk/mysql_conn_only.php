<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

/* opens a connection to the database */
function mysql_conn() {
	include 'mysql.php';
	$c = mysql_connect($server,$username,$password);
	if (!mysql_select_db($database)) {
		/* fail */
		$err_num = mysql_errno();
		switch ($err_num) {
			case 1049: // database not found
				mysql_query('create database '.$database.';');
				mysql_select_db($database);
				break;
				/* else */
				echo 'Failed to create database.';
				exit;
		}
	}
	return $c;
}

include_once 'magic_quotes.php';

$mysql_conn = mysql_conn();

?>