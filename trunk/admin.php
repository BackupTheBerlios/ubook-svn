<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

require_once 'mysql_conn.php';

function number_of_searches() {
	$r = mysql_query('select count(*) from searches;');
	if (!$r) return 0;
	$countRow = mysql_fetch_row($r);
	if (!$countRow) return 0;
	return $countRow[0];
}

function number_of_servers() {
	$result = mysql_query('select count(url) from servers where url != "";');
	if (!$result) return 0;
	$countArr = mysql_fetch_row($result);
	if (!$countArr) return 0;
	return $countArr[0];
}

require_once 'books/AbstractBookList.php';
$numberOfBooks = AbstractBookList::numberOfAllBooks();

require_once 'tools/Categories.php';
$categories = new Categories();
$numberOfCategories = sizeof($categories->getArray());

require_once 'notification/Searches.php';
$searches = new Searches();
$numberOfSearches = number_of_searches();

require_once 'net/LocalServer.php';
$serverName = new LocalServer();

$numberOfServers = number_of_servers();

/*
 * Optionen anbieten:
 * MySQL-Setup, Bücher betrachten, Kategorien verändern, Serveraustausch aktivieren, Mail verschicken.
 */
require 'header.php';

?>
<h2>uBook läuft</h2>
<table align="center">
<tr><td>Buchangebote</td><td><?php echo $numberOfBooks; ?></td><td><a href="admin_view.php">alle einsehen</a></td></tr>
<tr><td>Kategorien</td><td><?php echo $numberOfCategories; ?></td><td><a href="admin_categories.php">verwalten</a></td></tr>
<?php if ($searches->areActivated()) { ?>
<tr><td>Gespeicherte Suchen</td><td><?php echo $numberOfSearches; ?></td><td><a href="admin_notification.php?deactivate=1">alle löschen</a></td></tr>
<?php } else { ?>
<tr><td colspan="3"><a href="admin_notification.php?activate=1">Suchbenachrichtigungen aktivieren</a></td></tr>
<?php } ?>
<?php if ($serverName->isEmpty()) { ?>
<tr><td colspan="3"><a href="admin_servers.php">Suche an anderen Standorten aktivieren</a></td></tr>
<?php } else { ?>
<tr><td>Bekannte Standorte</td><td><?php echo $numberOfServers; ?></td><td><a href="admin_servers.php">einsehen</a></td></tr>
<?php } ?>
<tr><td colspan="3"><a href="admin_mail.php">E-Mail an alle Mailadressen versenden</a></td></tr>
</table>

<?php require 'footer.php'; ?>
