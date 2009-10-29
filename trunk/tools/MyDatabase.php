<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

/*
 * Connects to a MySQL-Database. Checks configuration and provides information
 * about it.
 */
class MyDatabase {

	private static $connection = null;

    /*
     * Opens a connection to the database.
     */
	public static function connect() {
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
		self::$connection = $c;
		return $c;
	}

	/*
	 * Returns the connection handler.
	 */
	public static function getConnection() {
		return self::$connection;
	}

}
?>
