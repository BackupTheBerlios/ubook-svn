<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */
 
 function webdir() {
  $webdir = $_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/';
  $webdir = 'http://'.str_replace('//','/',$webdir);
  return $webdir;
 }
 
?>
