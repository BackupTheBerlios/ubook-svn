<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
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
