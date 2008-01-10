<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

include_once 'mysql_conn.php';
include_once 'func_book.php';

// send mail
function send($book) {
	if (!isset($_POST['name'])) return false; // placeholder for robots
	$user_mail = stripslashes($_POST['name']);
	if (!strstr($user_mail,'@')) return true;
	include_once 'func_mail.php';
	$subject = 'Anfrage: ';
	$message = 'Es hat jemand mit der E-Mailadresse "'.$user_mail.'" Interesse für unten stehendes Buch bekundet.';
	if (isset($_POST['user_text']) && $_POST['user_text']) {
		$message .= ' Folgende Nachricht wurde mitgesandt:'."\n\n";
		$message .= stripslashes($_POST['user_text'])."\n";
	}
	$booked = bookmail($book,$subject,$message);
	header('Location: book.php?id='.$book['id'].'&booked='.$booked);
	return false;
}


if (!isset($_GET['id'])) exit;
$book_id = (int) $_GET['id'];
$result = mysql_query('select id,author,title,year,price,description,auth_key,mail from books where id="'.$book_id.'"');
if (mysql_num_rows($result) == 0) exit;
$book = mysql_fetch_array($result);
$result = mysql_query('select category from book_cat_rel where book_id="'.$book_id.'"');
$category_string = '';
$cat_seperator = '';
while ($row = mysql_fetch_array($result)) {
	$category_string .= $cat_seperator;
	$category_string .= $row['category'];
	$cat_seperator = ', ';
}
format_book($book);

$mail_error = send($book);

$user_key = '';
if (isset($_GET['key'])) {
	$user_key = $_GET['key'];
}

include 'header.php';
?>
<div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
  <?php if (isset($_GET['new'])) { ?>
  <div class="infobox"><h2>Das Buch wurde erfolgreich eingetragen!</h2>Du bekommst bald eine E-Mail, mit der du das Angebot &auml;ndern und auch l&ouml;schen kannst.<br/>...und so siehts aus:</div>
  <?php } ?>
  <?php if ($mail_error) { ?>
  <div class="infobox"><h2>E-Mailadresse unvollständig!</h2>Trage unten deine komplette E-Mailadresse ein, damit der Buchanbieter dir auch antworten kann.</div>
  <?php } ?>
  <?php if (isset($_GET['renew'])) { ?>
  <div class="infobox">
  Das Buchangebot wurde 
  <?php if ($_GET['renew'] == 0) { ?>
  <b>nicht</b>
  <?php } ?>
  erneuert.
  </div>
  <?php } ?>
  <div class="book">
   <h2>
    <?php
    echo $book['title'];
    ?>
   </h2>
   <table align="center">
    <tr><td>Autor:</td><td><?php echo $book['author']; ?></td></tr>
    <tr><td>Titel:</td><td><?php echo $book['title']; ?></td></tr>
    <tr><td>Preis:</td><td><?php echo $book['price']; ?> &euro;</td></tr>
    <tr><td>Erscheinungsjahr:</td><td><?php echo $book['year']; ?></td></tr>
    <tr><td>Kategorie:</td><td><?php echo $category_string; ?></td></tr>
    <tr><td colspan="2" style="max-width:35em;"><?php echo nl2br($book['description']); ?></td></tr>
   </table>
  </div>
  <?php if (isset($_GET['booked'])) { ?>
   <?php  if ($_GET['booked']) { ?>
   <div class="infobox">Dem Anbieter wurde erfolgreich eine E-Mail gesendet.</div>
   <?php } else { ?>
   <div class="infobox">Dem Anbieter konnte keine E-Mail gesendet werden.</div>
   <?php } ?>
  <?php } else { ?>
  <?php if ($user_key) { ?>
  <p>
  <form action="edit.php?id=<?php echo $book['id']; ?>&key=<?php echo $user_key; ?>" method="POST" style="display:inline">
    <input type="submit" value="Angebot ändern" />
  </form>
  <form action="delete.php?id=<?php echo $book['id']; ?>&key=<?php echo $user_key; ?>" method="POST" style="display:inline">
    <input type="submit" value="Angebot löschen" />
  </form>
  </p>
  <?php } else { ?>
  <h2>Das will ich haben!</h2>
  <form action="book.php?id=<?php echo $book['id']; ?>" method="post">
   <fieldset>
    <label for="name">Meine E-Mailadresse:</label><input type="text" name="name" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" size="25" /><!-- placeholder for robots -->
    <label for="user_text">Nachricht an den Anbieter:</label><textarea name="user_text" cols="25" rows="7"><?php if (isset($_POST['user_text'])) echo $_POST['user_text']; ?></textarea>
    <p><input type="submit" value="Dem Anbieter senden" /></p>
   </fieldset>
  </form>
  <?php } ?>
  <?php } ?>
 </body>
</html>