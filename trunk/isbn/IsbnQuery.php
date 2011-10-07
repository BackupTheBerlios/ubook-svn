<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
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
require_once 'IsbnDbProvider.php';
require_once 'LocalIsbnProvider.php';
require_once 'HttpProviders.php';

require_once 'books/Book.php';

/**
 * Fetches information about a book from different APIs.
 * @author maikel
 */
class IsbnQuery implements IsbnDbProvider {

    /**
     * {@inheritdoc }
     * @param Isbn $isbn to search for
     * @return Book containing found data
     */
    public static function query(Isbn $isbn) {
        $localProvider = new LocalIsbnProvider();
        $book = $localProvider->query($isbn);
        if ($book) {
            return $book;
        }
        $providers = HttpProviders::createProviders();
        foreach ($providers as $i => $p) {
            $p->provideBookFor($isbn);
        }
        foreach ($providers as $i => $p) {
            $book = $p->getBook();
            if ($book) {
                return $book;
            }
        }
        return new Book(array(
            'isbn' => $isbn->toString()
        ));
    }

}

?>