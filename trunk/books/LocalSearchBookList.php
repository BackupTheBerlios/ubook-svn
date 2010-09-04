<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'BookQuery.php';
require_once 'LocalBookList.php';
require_once 'SearchKey.php';

class LocalSearchBookList extends LocalBookList {

    private $key;

    public function __construct(SearchKey $searchKey) {
        $this->key = $searchKey;
    }

    /**
     * Generates a MySQL select statement.
     *
     * @param string $searchKey user given search key
     * @return MySQL select statement
     */
    protected function createMysqlQuery() {
        $searchKey = $this->key->asText();
        $option = $this->key->getOption();
        $query = BookQuery::searchQuery($searchKey);
        if ($option == 'new') {
            $query .= ' order by created desc limit 7';
        } else if ($option == 'random') {
            $query .= ' order by rand() limit 7';
        } else {
            $query .= ' order by author, title, price';
        }
        return $query;
    }

}

?>