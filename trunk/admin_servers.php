<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';
require_once 'net/ExternalServer.php';
require_once 'net/ExternalServerPool.php';
require_once 'net/LocalServer.php';

$localServer = new LocalServer();

if (isset($_POST['local_name'])) {
	$reset = false;
	if ($localServer->isEmpty()) $reset = true;
	$localServer->update($_POST['local_name']);
	if ($reset) {
		header('Location: admin_servers.php?reset_servers=1');
	}
	else {
		header('Location: admin_servers.php');
	}
}

if (!$localServer->isEmpty()) {
	$serverPool = new ExternalServerPool(true, true);

	if (isset($_GET['reset_servers'])) {
		$serverPool->resetDb();
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['remember'])) {
		$localServer->setRemembering((bool) $_GET['remember']);
		Header('Location: admin_servers.php');
	}

	if (isset($_GET['add_suggested'])) {
		$localServer->setAccepting((bool) $_GET['add_suggested']);
		Header('Location: admin_servers.php');
	}

	if (isset($_POST['new_url'])) {
		$new_url = $_POST['new_url'];
		if (strlen($new_url) > 7) {
			mysql_query('insert into servers (name, url) values ("'.$_POST['new_url'].'", "'.$_POST['new_url'].'");');
			Header('Location: admin_servers.php');
		}
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

<?php if ($localServer->isEmpty() || isset($_GET['edit_name'])) { ?>
<h2>Namen des Standorts festlegen</h2>
<div class="text">Falls die Suche in der lokalen Datenbank keine Treffer
ermittelt, können andere uBook-Webseiten abgefragt werden. Im Gegenzug
können andere Webseiten dann auch Suchanfragen hierhin schicken. Dafür
wird ein eindeutige Bezeichnung des Standorts gebraucht, zum Biespiel
den Namen der Stadt.<br />
<form action="admin_servers.php" method="post"><label for="local_name">Eindeutiger
Name:</label> <input type="text" name="local_name"
	value="<?php echo $localServer->name(); ?>" /> <input type="submit"
	value="Eintragen" /></form>
</div>
<?php } else { ?>
<h2>Standort <?php echo $localServer->name(); ?></h2>
<div class="menu"><span><a href="admin_servers.php?edit_name=1">Namen
des Standorts ändern.</a></span></div>


<h2>Automatische Standortverwaltung</h2>
<p>Unbekannte Standorte deaktiviert merken: <?php if ($localServer->rememberNewServers()) { ?>
aktiv &harr; <a href="admin_servers.php?remember=0">deaktivieren</a> <?php } else {?>
<a href="admin_servers.php?remember=1">aktivieren</a> &harr; deaktiviert
<?php }?></p>

<p>Empfohlene Standorte von anderen übernehmen: <?php if ($localServer->acceptSuggestedServers()) { ?>
aktiv &harr; <a href="admin_servers.php?add_suggested=0">deaktivieren</a>
<?php } else {?> <a href="admin_servers.php?add_suggested=1">aktivieren</a>
&harr; deaktiviert <?php }?></p>


<h2>Bekannte Standorte: <?php echo $serverPool->size(); ?></h2>
<?php if ($serverPool->size() == 0) { ?>
<div class="menu"><span><a href="admin_servers.php?reset_servers=1">Alle
Standorteinträge zurücksetzen.</a></span></div>
<?php } else { ?>
<ul class="text">
<?php echo $htmlList; ?>
	<li>
	<form action="admin_servers.php" method="post"><input type="text"
		name="new_url" value="http://" class="fullsize" /><input type="submit"
		value="Eintragen" /></form>
	</li>
</ul>
<?php } ?>

<?php } ?>

<?php require 'footer.php'; ?>
