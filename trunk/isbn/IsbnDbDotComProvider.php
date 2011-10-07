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

require_once 'Isbn.php';
require_once 'HttpIsbnDbProvider.php';

require_once 'books/Book.php';
require_once 'net/HttpUrl.php';

/**
 * Fetches information about a book from the API of isbndb.com.
 * @author maikel
 */
class IsbnDbDotComProvider extends HttpIsbnDbProvider {

    private $accessKey;

    public function  __construct($accessKey) {
        $this->accessKey = $accessKey;
    }

    protected  function urlFor(Isbn $isbn) {
        $urlString = 'http://isbndb.com/api/books.xml?'
                . 'access_key=' . $this->accessKey
                . '&index1=isbn&value1=' . $isbn->toString();
        return new HttpUrl($urlString);
    }

    protected function bookFor($xmlString) {
        try {
            $xml = @new SimpleXMLElement($xmlString);
        } catch (Exception $ex) {
            // malformed xml
            return;
        }
        if (!isset($xml->BookList)) return;
        if (!isset($xml->BookList->BookData)) return;
        $xmlBook = $xml->BookList->BookData[0];
        return new Book(array(
                        'author' => (string) $xmlBook->AuthorsText,
                        'title' => (string) $xmlBook->Title,
                        'isbn' => (string) $xmlBook['isbn']
        ));
    }

}

?>