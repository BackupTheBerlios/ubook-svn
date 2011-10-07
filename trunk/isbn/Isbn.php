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

/**
 * Represents an ISBN. It contains only valid characters, but no further
 * validation is done.
 * @author maikel
 */
class Isbn {

    private $string;

    public function __construct($isbnString) {
        if (!self::containsValidChars($isbnString)) {
            throw new Exception('The ISBN string contains bad chars.');
        }
        $this->string = (string) $isbnString;
    }

    public function toString() {
        return $this->string;
    }

    public static function fromPost() {
        if (!isset($_POST['isbn'])) return null;
        try {
            return new self($_POST['isbn']);
        } catch (Exception $ex) {
            return null;
        }
    }

    public static function stringFromPost() {
        $isbn = self::fromPost();
        if (!$isbn) return '';
        return $isbn->toString();
    }

    /**
     * Proofs, if an ISBN string contains only valid chars.
     * @param string $isbnString to check
     * @return boolean true, if $isbn contains only valid chars
     */
    public static function containsValidChars($isbnString) {
        if (preg_match('/^[0-9- ]{9,17}[xX]?$/', $isbnString) > 0) {
            return true;
        } else {
            return false;
        }
    }

}


?>