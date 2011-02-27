<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/UsersBooks.php';
require_once 'tools/Mailer.php';

function sendSummary() {
	$userMail = Mailer::mailFromUser('mail');
	if (!$userMail) {
		return false;
	}
        $userMail = stripslashes($userMail);
	$mailText = 'Hallo,'."\n".'hier eine Zusammenfassung aller Bücher, die mit deiner E-Mailadresse angeboten werden.';
	$books = new UsersBooks($userMail);
	$mailText.= $books->toString();
	return Mailer::mail($userMail, 'Deine Angebote', $mailText);
}

$sent = sendSummary();

/* Display after action */

include 'header.php';
?>

  <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Tipps</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>

<?php if ($sent) { ?>
  <h2>Zusammenfassung gesendet</h2>
  <div class="text">
   An die angegebene E-Mailadresse wurde eine Nachricht versendet. Darin sind alle Bücher aufgelistet, die mit dieser E-Mailadresse angeboten werden.
  </div>
<?php } else { ?>
  <h2>Senden fehlgeschlagen</h2>
  <div class="text">
   An die angegebene E-Mailadresse konnte keine Nachricht versendet werden.
  </div>
   <form action="summary.php" method="post">
    <input name="mail" type="text" value="<?php echo $_POST['mail']; ?>"/><br />
    <input type="submit" value="Erneut versuchen" />
   </form>
<?php } ?>

<?php include 'footer.php'; ?>