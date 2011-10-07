<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2007 Maikel Linke
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
 include_once 'mysql_conn.php';

 if (!isset($_GET['id'])) exit;
 if (!isset($_GET['key'])) exit;

 $id = (int) $_GET['id'];
 $key = $_GET['key'];

 $query = 'delete from books where id="'.$id.'" and auth_key="'.$key.'"';
 mysql_query($query);
 $success = mysql_affected_rows();
 if ($success > 0) {
     mysql_query('delete from book_cat_rel where book_id="' . $id . '"');
 }
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
   <span><a href="help.php">Tipps</a></span>
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
<?php include 'footer.php'; ?>
