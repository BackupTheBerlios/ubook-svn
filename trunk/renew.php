<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
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

require_once 'tools/MyDatabase.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

MyDatabase::connect();

//$query = 'update books set expired=null, expires = date_add(now(), interval 45 day) where id="'.$id.'" and auth_key="'.$key.'"';
$query = 'update books set expired=null, expires = date_add(now(), interval datediff(expires,created) day) where id="'.$id.'" and auth_key="'.$key.'"';
$success = mysql_query($query);

header('Location: book.php?id='.$id.'&key='.$key.'&renew='.$success);

?>