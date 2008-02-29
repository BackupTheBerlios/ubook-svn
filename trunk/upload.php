<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'mysql_conn.php';
require_once 'Image.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

$query = 'select id from books where id="'.$id.'" and auth_key="'.$key.'";';
$result = mysql_query($query);

if (mysql_num_rows($result) != 1) exit;
// now we have valid access

$image = new Image($id);
if ($image->moveUploaded()) {
	header('Location: book.php?id='.$id.'&key='.$key.'&uploaded=true');
}

if (isset($_GET['delete'])) {
	$delete = (bool) $_GET['delete'];
	if ($delete == true) {
		$image->delete();
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
  <div><?php echo $image->imgTag($id); ?></div>
  <?php if (isset($delete) && $delete == false) { ?>
  <div class="infobox">Soll das Bild komplett entfernt werden?</div>
  <form action="upload.php?id=<?php echo $_GET['id']; ?>&amp;key=<?php echo $_GET['key']; ?>&amp;delete=1" method="post">
   <input type="submit" value="Löschen" />
  </form>
  <form action="upload.php?id=<?php echo $_GET['id']; ?>&amp;key=<?php echo $_GET['key']; ?>" method="post">
   <input type="submit" value="Abbrechen" />
  </form>
  <?php } else { ?>
  <form action="upload.php?id=<?php echo $_GET['id']; ?>&amp;key=<?php echo $_GET['key']; ?>" method="post" enctype="multipart/form-data">
   <input name="image" type="file" size="50" accept="image/gif, image/jpeg, image/png" style="border: 0;" /><br />
   <input type="submit" value="Hochladen" />
  </form>
  <form action="upload.php?id=<?php echo $_GET['id']; ?>&amp;key=<?php echo $_GET['key']; ?>&amp;delete=0" method="post">
   <input type="submit" value="Löschen" />
  </form>
  <?php } ?>
 </body>
</html>
