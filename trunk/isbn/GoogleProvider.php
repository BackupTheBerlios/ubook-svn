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

require_once 'HttpIsbnDbProvider.php';

require_once 'books/Book.php';
require_once 'net/HttpUrl.php';

/**
 * Fetches information about a book from the API of Google Books.
 * @author maikel
 */
class GoogleProvider extends HttpIsbnDbProvider {

    private $isbn;

    protected function urlFor(Isbn $isbn) {
        $this->isbn = $isbn;
        $urlString = 'http://books.google.com/books/feeds/volumes'
                . '?q=isbn%3A' . $isbn->toString();
        return new HttpUrl($urlString);
    }

    protected function bookFor($atomPub) {
        try {
            $xml = @new SimpleXMLElement($atomPub);
        } catch (Exception $ex) {
            // malformed xml
            return;
        }
        if (!isset($xml->entry)) return;
        $xmlBook = $xml->entry[0];
        $bookArray = array();
        $dcChilds = $xmlBook->children('dc', true);
        $authors = array();
        foreach ($dcChilds->creator as $a) {
        	$names = explode(' ', $a);
        	$last = end($names);
        	unset($names[sizeof($names)-1]);
        	$authors[] = $last . ', ' . implode(' ', $names);
        }
        $year = substr($dcChilds->date, 0, 4);
        return new Book(array(
                        'author' => implode(' and ', $authors),
                        'title' => (string) $dcChilds->title,
                        'year' => $year,
                        'isbn' => $this->isbn->toString()
        ));
    }

}

?>
