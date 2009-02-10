<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */
 
$books = '';

require_once 'mysql_conn.php';
$query = 'select id, auth_key, author, title from books order by author, title, price';
$result = mysql_query($query);
while ($book = mysql_fetch_array($result)) {
	$books.= '<li>';
	$books.= '<a href="book.php?id='.$book['id'].'&amp;key='.$book['auth_key'].'">';
	$books.= $book['author'];
	$books.= ': ';
	$books.= $book['title'];
	$books.= '</a>';
	$books.= '</li>'."\n";
}

require 'header.php';

?>

<?php if ($error == '') {?>
  <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Hilfe</a></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
   <?php if ($books == '') {?>
    <p>Das Setup ist fertig.</p>
   <?php } else { ?>
    <h2>Alle Bücher</h2>
    <ol class="text">
     <?php echo $books; ?>
    </ol>
   <?php } ?>
  <?php } ?>
  
  
  <?php if ($error == 'not writeable') {?>
   <p>In diesem Verzeichnis muss die Konfigurationsdatei "mysql.php" geschrieben werden. Dazu braucht der Webserver das Schreibrecht für dieses Verzeichnis. Vergib das Schreibrecht und es geht weiter.</p>
   <p><form action="admin.php" method="get"><input type="submit" value="Weiter" /></form></p>
  <?php } ?>
  
  <?php if ($error == 'no file') {?>
  <p>Dieses Programm braucht Zugang zu einer MySQL-Datenbank.</p>
  <form action="admin.php" method="post">
   <table align="center" style="width:35em;">
    <tr><td>MySQL-Server</td><td><input type="text" name="server" value="localhost" /></td></tr>
    <tr><td>Benutzername</td><td><input type="text" name="username" value="ubook" /></td></tr>
    <tr><td>Passwort</td><td><input type="password" name="password" value="" /></td></tr>
    <tr><td>Datenbank</td><td><input type="text" name="database" value="ubook" /></td></tr>
   </table>
   <p><input type="submit" value="Weiter" /></p>
  </form>
  <?php } ?>
  
 </body> 
</html>

