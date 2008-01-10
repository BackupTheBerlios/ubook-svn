<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */
 
 function format_book(&$book) {
  $book['price'] = str_replace('.',',',$book['price']);
  return $book;
 }

 function fetch_book($mysql_result) {
  $book = mysql_fetch_array($mysql_result);
  if ($book == null) return;
  format_book($book);
  return $book;
 }

?>