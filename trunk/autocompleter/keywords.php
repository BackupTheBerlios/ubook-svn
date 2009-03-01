<?php 
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once '../mysql_conn_only.php';

$keywords = $_POST['value'];

$query = 'select author, title from books'
. ' where author like "' . $keywords . '%"'
. ' or title like "' . $keywords . '%"'
. ' group by author, title'
. ' order by author, title;';
$result = mysql_query($query);
while ($a = mysql_fetch_array($result)) {
	echo '<li>' . $a['author'] . ' ' . $a['title'] . '</li>';
}

?>