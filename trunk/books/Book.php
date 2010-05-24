<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'tools/Parser.php';
require_once 'tools/Template.php';

class Book {

    private static $keys = array('id', 'isbn', 'author', 'title', 'year',
            'price', 'description', 'mail', 'auth_key', 'created');

    private $data = array();

    /**
     * Tries to fetch a Book from the result set.
     * @param resource $result MySQL resource
     * @return Book with data from MySQL or null
     */
    public static function fromMySql(&$result) {
        $bookArray = mysql_fetch_array($result);
        if (!$bookArray) return null;
        $bookArray['price'] = str_replace('.', ',', $bookArray['price']);
        return new self($bookArray);
    }

    public function __construct($assocArray = array()) {
        foreach (self::$keys as $k) {
            $this->data[$k] = isset($assocArray[$k]) ? $assocArray[$k] : '';
        }
    }

    public function get($field) {
        if (!isset($this->data[$field])) {
            throw new Exception('A book does not have this: ' . $field);
        }
        return $this->data[$field];
    }

    public function set($field, $value) {
        if (!isset($this->data[$field])) {
            throw new Exception('A book does not have this: ' . $field);
        }
        $this->data[$field] = $value;
    }

    public function assignHtmlToTemplate(Template $tmpl) {
        foreach ($this->data as $k => $v) {
            $tmpl->assign($k, Parser::text2html($v));
        }
    }

    public function asText() {
        $tmpl = Template::fromFile('view/book.txt');
        foreach ($this->data as $k => $v) {
            $tmpl->assign($k, $v);
        }
        return $tmpl->result();
    }

}

?>