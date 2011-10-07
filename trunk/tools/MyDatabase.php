<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
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

/*
 * Security: Force quoting of GET and POST variables.
 */
include_once 'magic_quotes.php';

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
        self::$connection = @mysql_connect($server,$username,$password);
        if (!self::$connection) return;
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
	}

	/*
	 * Returns the connection handler.
	 */
	public static function getConnection() {
		return self::$connection;
	}

}
?>
