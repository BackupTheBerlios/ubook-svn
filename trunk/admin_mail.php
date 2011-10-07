<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2008 Maikel Linke
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

$books = '';

require_once 'mysql_conn.php';

if (isset($_POST['subject'])) {
	require_once 'books/UsersBooks.php';
	require_once 'tools/Mailer.php';
	$subject = stripslashes($_POST['subject']);
	$text = stripslashes($_POST['text']);
	$query = 'select distinct mail from books';
	$result = mysql_query($query);
	$user_number = mysql_num_rows($result);
	$sent_mails = 0;
	while ($mail_row = mysql_fetch_row($result)) {
		$mail = $mail_row[0];
		$bookList = new UsersBooks($mail);
		$books = $bookList->toString();
		$mail_text = $text.$books;
		$success = Mailer::mail($mail,$subject,$mail_text);
		if ($success) $sent_mails++;
	}
	header('Location: admin_mail.php?sent_mails='.$sent_mails.'&user_number='.$user_number);
}

include 'header.php';

?>

<div class="menu"><span><a href="admin.php">&larr; Zurück zur
Administrationsübersicht</a></span></div>

  <?php if (isset($_GET['sent_mails'])) { ?>
  <div class="infobox">
   Es wurden <?php echo $_GET['sent_mails']; ?> von <?php echo $_GET['user_number']; ?> E-Mails verschickt.
  </div>
  <?php } ?>

  <fieldset>
  <legend>Mail an alle NutzerInnen verschicken...&nbsp;</legend>
  <form action="admin_mail.php" method="post">
    <label>Betreff<br/>
    <input type="text" name="subject" value=""/>
    </label>

    <label>Text<br/>
    <textarea name="text" cols="24" rows="10"></textarea>
    </label>
    <br/>
      <input type="submit" value="Verschicken" />
  </form>
  </fieldset>
<?php include 'footer.php'; ?>
