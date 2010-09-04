<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'mysql_conn.php';

function createDbVersionTable() {
    $query = 'create table db_version (db_version date not null);';
    mysql_query($query);
    $query = 'insert into db_version values ("2010-09-03");';
    mysql_query($query);
}

createDbVersionTable();
?>
