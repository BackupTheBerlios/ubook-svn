<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';
require_once 'books/ExternalServer.php';
require_once 'books/ExternalServerPool.php';
require_once 'LocalServerName.php';

$localServerName = new LocalServerName();

if (isset($_POST['local_name'])) {
	$localServerName->update($_POST['local_name']);
	header('Location: admin_servers.php?reset_servers=1');
}

if (!$localServerName->isEmpty()) {
	$serverPool = new ExternalServerPool(true, true);

	if (isset($_GET['reset_servers'])) {
		$serverPool->resetDb();
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['set_manual'])) {
		$serverPool->disableAcceptingServers();
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['set_automatic'])) {
		$serverPool->enableAcceptingServers();
		Header('Location: admin_servers.php');
	}

	if (isset($_POST['new_url'])) {
		$serverPool->addUrl($_POST['new_url']);
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['blacklist'])) {
		ExternalServer::blacklist($_GET['blacklist']);
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['activate'])) {
		ExternalServer::activate($_GET['activate']);
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['delete'])) {
		ExternalServer::delete($_GET['delete']);
		Header('Location: admin_servers.php');
	}

	$htmlList = '';
	while ($server = $serverPool->next()) {
		$blackListOption = 'blacklist';
		if ($server->isBlacklisted()) {
			$blackListOption = 'activate';
		}
		$htmlList .= '<li>'
		. $server->toHtmlLink()
		. '&nbsp;&nbsp;<a href="admin_servers.php?'
		. $blackListOption . '=' . $server->getUrl() .'">'
		. '[['.$blackListOption.']]</a>'
		. '</a>'
		. '&nbsp;&nbsp;<a href="admin_servers.php?delete=' . $server->getUrl() .'">'
		. '[[delete]]</a>'
		. '</li>'."\n";
	}
}

require 'header.php';

?>

<div class="menu"><span><a href="admin.php">&larr; Zurück zur
Administrationsübersicht</a></span></div>

<?php if ($localServerName->isEmpty() || isset($_GET['edit_name'])) { ?>
<h2>Namen des Standorts festlegen</h2>
<div class="text">Falls die Suche in der lokalen Datenbank keine Treffer
ermittelt, können andere uBook-Webseiten abgefragt werden. Im Gegenzug
können andere Webseiten dann auch Suchanfragen hierhin schicken. Dafür
wird ein eindeutige Bezeichnung des Standorts gebraucht, zum Biespiel
den Namen der Stadt.<br />
<form action="admin_servers.php" method="post"><label for="local_name">Eindeutiger
Name:</label> <input type="text" name="local_name"
	value="<?php echo $localServerName->name(); ?>" /> <input type="submit"
	value="Eintragen" /></form>
</div>
<?php } else { ?>
<h2>Standort <?php echo $localServerName->name(); ?></h2>
<div class="menu"><span><a href="admin_servers.php?edit_name=1">Namen
des Standorts ändern.</a></span></div>
<?php if ($serverPool->acceptMoreServers()) { ?>
<h2>Automatisches Hinzufügen von anderen Standorten aktiv</h2>
<div class="menu"><span><a href="admin_servers.php?set_manual=1">Das automatische Hinzufügen von Standorten deaktivieren</a></span></div>
<?php } else { ?>
<h2>Manuelle Verwaltung der Standortliste</h2>
<div class="menu"><span><a href="admin_servers.php?set_automatic=1">Das automatische Hinzufügen von Standorten aktivieren</a></span></div>
<?php } ?>
<h2>Bekannte Standorte: <?php echo $serverPool->size(); ?></h2>
<?php if ($serverPool->size() == 0) { ?>
<div class="menu"><span><a href="admin_servers.php?reset_servers=1">Alle
Standorteinträge zurücksetzen.</a></span></div>
<?php } else { ?>
<ul class="text">
<?php echo $htmlList; ?>
<li><form action="admin_servers.php" method="post"><input type="text" name="new_url" value="http://" class="fullsize" /><input type="submit" value="Eintragen" /></form></li>
</ul>
<?php } ?>

<?php } ?>

<?php require 'footer.php'; ?>
