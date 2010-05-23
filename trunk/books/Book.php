<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

class Book {

    private static $keys =
            array('isbn', 'author', 'title', 'year', 'price', 'description');

    private $data = array();

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

    public function assignToTemplate(Template $tmpl) {
        $tmpl->assignArray($this->data);
    }

}

?>