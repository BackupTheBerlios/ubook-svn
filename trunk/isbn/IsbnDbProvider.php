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

require_once 'net/DownloadThread.php';

/**
 * Provides information about an ISBN database.
 * @author maikel
 */
interface IsbnDbProvider {

    /**
     * Tries to get information about a book with the given ISBN.
     *
     * @param Isbn $isbn to search for
     * @return Book containing found data
     */
    public static function query(Isbn $isbn);
}

?>