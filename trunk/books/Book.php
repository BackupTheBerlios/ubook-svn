<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

class Book {

    private $data;

    public function __construct($author = '', $title = '', $year = '',
            $price = '', $isbn = '', $description = '') {
        $this->data = array(
                'isbn' => $isbn,
                'author' => $author,
                'title' => $title,
                'year' => $year,
                'price' => $price,
                'description' => ''
        );
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
        foreach ($this->data as $key => $value) {
            $tmpl->assign($key, $value);
        }
    }

}

?>