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

require_once 'PHPUnit/Framework.php';
require_once 'books/Book.php';
require_once 'isbn/Isbn.php';
require_once 'isbn/UBKarlsruheProvider.php';

class UBKarlsruheProviderTest extends PHPUnit_Framework_TestCase {

    function testUBKarlsruhe() {
        $isbn13 = new Isbn('978-3897215429');
        $expected = new Book(array(
                        'author' => 'Günther, Karsten',
                        'title' => 'LaTeX',
                        'year' => '2008',
                        'isbn' => $isbn13->toString()
        ));
        $prov = new UBKarlsruheProvider();
        $prov->provideBookFor($isbn13);
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>