<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

/* anti spam */
if (isset($_POST['name']) && $_POST['name'] != '') exit;

include_once 'mysql_conn.php';
include_once 'SelectableCategories.php';

// creates a random string with certain length
function random_key($l=32) {
	$char = array();
	for($i=48;$i<58;$i++) $char[] = chr($i);
	for($i=65;$i<91;$i++) $char[] = chr($i);
	for($i=97;$i<123;$i++) $char[] = chr($i);
	srand((double)microtime()*1000000);
	$s = '';
	for($i=0;$i<$l;$i++){
		$s.= $char[rand(0,sizeof($char)-1)];
	}
	return addslashes($s);
}

$selectableCategories = new SelectableCategories();

if (isset($_POST['author'])) {
	$mail = $_POST['author'];
	if ($mail && strstr($mail,'@')) {
		$key = random_key();
		$query = 'insert into books (author,title,year,price,description,mail,auth_key,created,expires) values (
   		"'.$_POST['mail'].'",
   		"'.$_POST['title'].'",
   		"'.$_POST['year'].'",
   		"'.str_replace(',','.',$_POST['price']).'",
  		"'.$_POST['description'].'",
		"'.$mail.'",
		"'.$key.'",
		now(),
		date_add(now(),interval 45 day)
		)';
		mysql_query($query);
		$book_id = mysql_insert_id();
		$selectableCategories->setBookId($book_id);
		$selectableCategories->update();
		$author = stripslashes($_POST['author']);
		$title = stripslashes($_POST['title']);
		$to = stripslashes($mail);
		$subject = '';
		$message = 'Mit deiner E-Mailadresse wurde das unten stehende Buch angeboten. Hebe diese E-Mail auf, um das Angebot später ändern und löschen zu können.';
		require_once 'Mailer.php';
		Mailer::send($book_id, $subject, $message);
		header('Location: book.php?id='.$book_id.'&key='.$key.'&new=1');
	}
}

$http_equiv_expires = 43200;
$navigation_links['first'] = array('Erste','add.php');
include 'header.php';
?>
 <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><b>Buch anbieten</b></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
  <fieldset>
  <legend>Buch anbieten...&nbsp;</legend>
  <form action="add.php" method="post">
    <input type="text" name="name" value="" class="boogy" />
    
    <label>Autorin / Autor<br/>
    <input type="text" name="mail" value="" class="fullsize" />
    </label>

    <label>Titel<br/>
    <input type="text" name="title" value="" class="fullsize" />
    </label>

    <div style="float:left; margin-right:2em;">
      <label>Erscheinungsjahr<br/>
      <input type="text" name="year" value="" size="6" />
      </label>
    </div>

    <div style="float:right; margin-bottom:0.5em;">
      <label>Dein Preis<br/>
      <input type="text" name="price" value="" size="6" /> &euro;</label>
    </div>

    <label style="clear:both;">Kategorien<br/>
    <?php echo $selectableCategories->createSelect(0); ?> <?php echo $selectableCategories->createSelect(1); ?></label>

    <label style="clear:both;">Deine E-Mailadresse<br/>
    <input type="text" name="author" value="" class="fullsize" /></label>

    <label>Weiteres<br/>
    <textarea name="description" cols="24" rows="10" class="fullsize"></textarea>
    </label>
    <br/>
      <input type="submit" value="Anbieten" />
  </form>
  <form action="./" method="get">
      <input type="submit" value="Abbrechen" />
  </form>
  </fieldset>
 </body>
</html>
