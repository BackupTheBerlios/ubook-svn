<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

class BookQuery {

    /**
     * Generates a MySQL select statement. The 'order by' parameter is missing
     * and can be added.
     *
     * @param string $searchKey user given search key
     * @return MySQL select statement
     */
    public static function searchQuery($searchKey) {
        $fields = 'concat(author, " ", title, " ", description) ';
        $keys = explode(' ', $searchKey);
        $and = ' ';
        $query = 'select id, author, title, price, year, description, created from books where ';
        foreach ($keys as $i => $k) {
            $query .= $and . $fields . 'like "%' . $k . '%"';
            $and = ' and ';
        }
        return $query;
    }

}

?>