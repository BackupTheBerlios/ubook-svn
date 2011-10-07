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

/**
 * Generates random keys.
 */
class KeyGenerator {

	/**
	 * Creates a random string with certain length.
	 * @param $l Desired length of the string
	 * @return string random chars
	 */
	public static function genKey($l=32) {
            /*
             * TODO: check md5(uniqid(mt_rand(), true))
             * More info at:
             * http://www.php.net/manual/de/function.uniqid.php
             */
		$char = array();
		for($i=48;$i<58;$i++) $char[] = chr($i);
		for($i=65;$i<91;$i++) $char[] = chr($i);
		for($i=97;$i<123;$i++) $char[] = chr($i);
		srand((double)microtime()*1000000);
		$s = '';
		for($i=0;$i<$l;$i++){
			$s.= $char[rand(0,sizeof($char)-1)];
		}
		return addslashes($s);
	}

}
?>
