<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

$http_equiv_expires = 43200;
include 'header.php';
?>
 <div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><b>Hilfe</b></span>
   <span><a href="about.php">Impressum</a></span>
  </div>
 <h2>Wie funktioniert das?</h2>
 <div class="text">
  Unter dem Punkt "Buch anbieten" kannst du ein Buch eintragen. Deine E-Mailadresse wird dabei gebraucht, damit du das Angebot später noch ändern oder löschen kannst. Sie wird aber nicht veröffentlicht. Dein Angebot wird zusammen mit allen anderen Buchangeboten gespeichert. Diese Einträge können unter "Buch suchen" durchsucht werden. Wenn du ein interessantes Buch gefunden hast, findest du auf der Buchseite unten ein Formular, mit dem du dem Anbieter des Buches deine E-Mailadresse und einen Text senden kannst. Daraufhin hat der Anbieter deine E-Mailadresse und kann dir antworten. So könnt ihr euch verabreden, um den Preis feilschen und den Kauf verhandeln.
 </div>
 <h2>Wie arbeitet die Suchfunktion?</h2>
 <div class="text">
  Die eingegebenen Zeichen werden als erstes in Worte aufgeteilt (an Leerzeichen getrennt).
  Jedes Wort muss dann in mindestens einem Datenfeld zumindest als Teilwort vorkommen.
  Durchsucht werden die Datenfelder Autor, Titel und Beschreibung. 
 </div>
 <h2>Ich habe keine Mail bekommen. Was nun?</h2>
 <div class="text">
  <p>1. Wenn du es noch nicht getan hast, dann schau mal in deinen Spamordner. Viele Filter sind falsch eingestellt.</p>
  <p>2. Du kannst bei deinem Angebot versuchen, dir selbst eine E-Mail zuzuschicken. Du musst jedoch die vorher eingetragene E-Mailadresse auch dort angeben, um den Änderungslink für dein Angebot zu bekommen. Wenn das auch nicht funktioniert, dann ist die eingetragene E-Mailadresse wohl falsch.</p>
  <p>3. Wenn die Adresse falsch ist, dann erstelle einfach ein neues Angebot. Das alte wird nach einer Weile automatisch gelöscht.</p>
 </div>
 <h2>Welche Bücher biete ich eigentlich an?</h2>
 <div class="text">
   Wenn du vergessen hast, welche Bücher du hier anbietest oder nochmal eine Zusammenfassung zugeschickt bekommen möchtest, dann trage deine E-Mailadresse in dieses Formular ein:<br />
 </div>
   <form action="summary.php" method="post">
    <input name="mail" type="text" class="fullsize" /><br />
    <input type="submit" value="Zusammenfassung schicken" />
   </form>
 <h2>Du hast noch Fragen?</h2>
 <div class="text">
  Dann frag uns: <a href="mailto:ubook-info@lists.berlios.de">ubook-info@lists.berlios.de</a>. Aber auch andere Kommentare und Kritik zur Bücherbörse sind willkommen.
 </div>
<?php include 'footer.php'; ?>