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

require_once 'BooklookerProvider.php';
require_once 'GoogleProvider.php';
require_once 'IsbnDbDotComProvider.php';
require_once 'UBKarlsruheProvider.php';

/**
 * Creates all available IsbnDbProviders.
 * @author maikel
 */
class HttpProviders {

	public static function createProviders() {
        $list = array();
        $list[] = new GoogleProvider();
        $list[] = new UBKarlsruheProvider();
        //$list[] = new BooklookerProvider('key needed');
        //$list[] = new IsbnDbDotComProvider('key needed');
        return $list;
	}

}

?>