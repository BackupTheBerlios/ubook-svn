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