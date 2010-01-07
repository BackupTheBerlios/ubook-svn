<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

/* anti spam */
if (isset($_POST['name']) && $_POST['name'] != '') exit;

require_once 'tools/KeyGenerator.php';
require_once 'tools/SelectableCategories.php';

include_once 'mysql_conn.php';

// generates output with select fields
function echoSelectableCategories($selectableCategories) {
	$selCatArray = $selectableCategories->createSelectArray();
	foreach ($selCatArray as $i => $selCat) {
		echo ' '.$selCat;
	}
}

$selectableCategories = new SelectableCategories();

$book = array(
'isbn' => '',
'author' => '',
'title' => '',
'year' => ''
);

if (isset($_POST['isbn'])) {
    require_once 'isbn/IsbnQuery.php';
    /* DANGER: check isbn */
	// TODO: Input check
    $book = IsbnQuery::query($_POST['isbn']);
}

if (isset($_POST['author'])) {
	require_once 'tools/Mailer.php';
	$mail = Mailer::mailFromUser('author');
	if ($mail && strstr($mail,'@')) {
		$quotedAuthor =  trim($_POST['mail']);
		$quotedTitle = trim($_POST['title']);
		$year = (int) trim($_POST['year']);
		$price = (float) str_replace(',', '.', $_POST['price']);
		$quotedDescription = $_POST['description'];
		$key = KeyGenerator::genKey();
		$query = 'insert into books'
		. ' (author, title, year, price, description, mail, auth_key'
		. ', created,expires)'
		. ' values ('
		. '"' . $quotedAuthor . '"'
		. ', "' . $quotedTitle . '"'
		. ', "' . $year . '"'
		. ', "' . $price . '"'
		. ', "' . $quotedDescription . '"'
		. ', "' . $mail . '"'
		. ', "' . $key . '"'
		. ', now()'
		. ', date_add(now(), interval 45 day)'
		. ')';
		mysql_query($query);
		$book_id = mysql_insert_id();
		$selectableCategories->setBookId($book_id);
		$selectableCategories->update();
		$subject = '';
		$message = 'Mit deiner E-Mailadresse wurde das unten stehende Buch angeboten. Hebe diese E-Mail auf, um das Angebot später ändern und löschen zu können.';
		require_once 'tools/Mailer.php';
		Mailer::send($book_id, $subject, $message);
		require_once 'notification/Searches.php';
		$searches = new Searches();
		if ($searches->areActivated()) {
			$author = stripslashes($quotedAuthor);
			$title = stripslashes($quotedTitle);
			$description = stripslashes($quotedDescription);
			$searches->bookAdded($book_id, $author, $title, $description);
		}
		header('Location: book.php?id='.$book_id.'&key='.$key.'&new=1');
	}
}

$http_equiv_expires = 43200;
$navigation_links['first'] = array('Erste','add.php');
include 'header.php';
?>
<div class="menu"><span><a href="./">Buch suchen</a></span> <span><b>Buch
anbieten</b></span> <span><a href="help.php">Tipps</a></span> <span><a
	href="about.php">Impressum</a></span></div>

<?php if (!isset($_POST['isbn'])) { ?>
<fieldset class="fullsize"><legend>Automatisch füllen...&nbsp;</legend>
<form action="add.php" method="post" name="isbn_form"><label>ISBN: <input
	type="text" name="isbn" value="" class="fullsize" /> </label> <input
	type="submit" value="Ausfüllen" /></form>
</fieldset>
<br />
<br />
<?php } ?>

<fieldset class="fullsize"><legend>Buch anbieten...&nbsp;</legend>
<form action="add.php" method="post" name="add_form"><input type="text"
	name="name" value="" class="boogy" /> <label>Nachname, Vorname der
Autorin / des Autor<br />
<input type="text" name="mail" value="<?php echo $book['author']; ?>" class="fullsize" /> </label> <label>Titel
des Buches<br />
<input type="text" name="title" value="<?php echo $book['title']; ?>" class="fullsize" /> </label>

<div style="float: left; margin-right: 2em;"><label>Erscheinungsjahr<br />
<input type="text" name="year" value="<?php echo $book['year']; ?>" size="6" /> </label></div>

<div style="margin-bottom: 0.5em;"><label>Dein Preis<br />
<input type="text" name="price" value="" size="6" /> &euro;</label></div>

<label style="clear: both;">Kategorien<br />
<?php echoSelectableCategories($selectableCategories); ?></label> <label
	style="clear: both;">Deine E-Mailadresse<br />
<input type="text" name="author" value="" class="fullsize" /></label> <label>Weiteres<br />
<textarea name="description" cols="24" rows="10" class="fullsize">
<?php if ($book['isbn']) {
	echo 'ISBN: ' . $book['isbn'] . "\n\n";
} ?></textarea>
</label> <br />
<input type="submit" value="Anbieten" /></form>
<form action="./" method="get"><input type="submit" value="Abbrechen" />
</form>
</fieldset>
<script type="text/javascript">
<?php
if (isset($_POST['isbn'])) {
   echo 'document.add_form.mail.focus();';
} else {
   echo 'document.isbn_form.isbn.focus();';
}
?>
</script>
<?php include 'footer.php'; ?>