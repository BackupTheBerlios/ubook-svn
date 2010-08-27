<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
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