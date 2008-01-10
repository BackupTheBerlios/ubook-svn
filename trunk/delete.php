<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */
 include_once 'mysql_conn.php';

 if (!isset($_GET['id'])) exit;
 if (!isset($_GET['key'])) exit;

 $id = (int) $_GET['id'];
 $key = $_GET['key'];

 $query = 'delete from books where id="'.$id.'" and auth_key="'.$key.'"';
 mysql_query($query);
 $success = mysql_affected_rows();
 if ($success == 0) {
  $result = mysql_query('select 1 from books where id="'.$id.'"');
  if (mysql_num_rows($result) == 0) {
   $success = -1;
  }
 }
 
 include 'header.php';
?>
  <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
  <p>
   Das Buch wurde 
  <?php if ($success == 1) { ?>
   entfernt.
  <?php } ?>
  <?php if ($success == 0) { ?>
   NICHT entfernt.
  <?php } ?>
  <?php if ($success == -1) { ?>
   nicht gefunden.
  <?php } ?>
  </p>
 </body>
</html>
