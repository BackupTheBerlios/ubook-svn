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

require_once 'mysql_conn.php';

$message = '';

if (isset($_GET['new_category']) && $_GET['new_category']) {
	$new_category = $_GET['new_category'];
	$q = 'insert into categories (category) values ("'.$new_category.'");';
	if (mysql_query($q)) {
		$message.= '<p>Kategorie wurde hinzugefügt.</p>';
	}
	else {
		$message.= '<p>Kategorie wurde <b>nicht</b> hinzugefügt.</p>';
	}
}

if (isset($_GET['rename'])) {
	$rename_cat = $_GET['rename'];
	if (isset($_GET['new_name']) && $_GET['new_name']) {
		$new_name = $_GET['new_name'];
		$q = 'update categories set category="'.$new_name.'" where category="'.$rename_cat.'";';
		mysql_query($q);
		$q = 'update book_cat_rel set category="'.$new_name.'" where category="'.$rename_cat.'";';
		mysql_query($q);
		unset($new_name);
		unset($rename_cat);
	}
}

if (isset($_GET['delete'])) {
	$delete_cat = $_GET['delete'];
	if (isset($_GET['delete_sure']) && $_GET['delete_sure'] === "yes") {
		$q = 'delete from book_cat_rel where category="'.$delete_cat.'";';
		mysql_query($q);
		$q = 'delete from categories where category="'.$delete_cat.'";';
		mysql_query($q);
		unset($delete_cat);
	}
}

$cats = '';
$query = 'select category from categories;';
$result = mysql_query($query);
while ($cat = mysql_fetch_array($result)) {
	$cats.= '<li>';
	$cats.= $cat['category'];
	$cats.= ' &nbsp;&nbsp;<a href="admin_categories.php?rename=';
	$cats.= $cat['category'];
	$cats.= '">[rename]</a>';
	$cats.= ' &nbsp;&nbsp;<a href="admin_categories.php?delete=';
	$cats.= $cat['category'];
	$cats.= '">[delete]</a>';
	$cats.= '</li>'."\n";
}

require 'header.php';

?>

  <div class="menu">
   <span><a href="admin.php">&larr; Zurück zur Administrationsübersicht</a></span>
  </div>

  <?php echo $message; ?>

  <?php if (isset($rename_cat)) { ?>
   <form action="admin_categories.php" method="get">
    <input type="hidden" name="rename" value="<?php echo $rename_cat; ?>" />
    <p><input type="text" name="new_name" size="16" maxlength="32" value="<?php echo $rename_cat; ?>" /> <input type="submit" value="Umbenennen" /></p>
   </form>
  <?php } ?>

  <?php if (isset($delete_cat)) { ?>
   <form action="admin_categories.php" method="get">
    <input type="hidden" name="delete" value="<?php echo $delete_cat; ?>" />
    <input type="hidden" name="delete_sure" value="yes" />
    <p><input type="submit" value="&quot;<?php echo $delete_cat ?>&quot; Löschen" /></p>
   </form>
  <?php } ?>

  <ol class="text">
  <?php echo $cats; ?>
  </ol>

  <form action="admin_categories.php" method="get">
   <p><input type="text" name="new_category" size="16" maxlength="32" /> <input type="submit" value="Hinzufügen" /></p>
  </form>

<?php include 'footer.php'; ?>

