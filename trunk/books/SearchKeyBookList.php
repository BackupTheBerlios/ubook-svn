<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'AbstractBookList.php';
require_once 'BookQuery.php';
require_once 'SearchKey.php';

require_once 'mysql_conn.php';

class SearchKeyBookList extends AbstractBookList {

    public function __construct(SearchKey $searchKey) {
        $searchQuery = self::searchQuery($searchKey->asText(), $searchKey->getOption());
        $result = mysql_query($searchQuery);
        parent::setSize(mysql_num_rows($result));
        $books = parent::mysqlResultToHtml(&$result);
        parent::setHtmlRows($books);
    }

    /**
     * Generates a MySQL select statement.
     *
     * @param string $searchKey user given search key
     * @return MySQL select statement
     */
    protected static function searchQuery($searchKey, $option) {
        $query = BookQuery::searchQuery($searchKey);
        if ($option == 'new') {
            $query .= ' order by created desc limit 7';
        }
        else if ($option == 'random') {
            $query .= ' order by rand() limit 7';
        }
        else {
            $query .= ' order by author, title, price';
        }
        return $query;
    }

}

?>