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

require_once 'tools/Parser.php';
require_once 'text/Template.php';

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