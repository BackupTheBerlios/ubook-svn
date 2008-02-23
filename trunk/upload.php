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
$image->moveUploaded();

include 'header.php';
?>
  <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
  <div><?php $image->echo_img_tag(); ?></div>
  <form action="upload.php?id=<?php echo $_GET['id']; ?>&amp;key=<?php echo $_GET['key']; ?>" method="post" enctype="multipart/form-data">
   <input name="image" type="file" size="50" accept="image/gif, image/jpeg, image/png" />
   <input type="submit" value="Hochladen" />
  </form>
 </body>
</html>
