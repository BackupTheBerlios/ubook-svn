<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */
include_once 'mysql_conn_only.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

//$query = 'update books set expired=null, expires = date_add(now(), interval 45 day) where id="'.$id.'" and auth_key="'.$key.'"';
$query = 'update books set expired=null, expires = date_add(now(), interval datediff(expires,created) day) where id="'.$id.'" and auth_key="'.$key.'"';
$success = mysql_query($query);

header('Location: book.php?id='.$id.'&key='.$key.'&renew='.$success);

?>