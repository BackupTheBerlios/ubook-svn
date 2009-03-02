<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'mysql_conn.php';
require_once 'net/ExternalServer.php';
require_once 'net/ExternalServerPool.php';
require_once 'net/LocalServer.php';

/**
 * Provides admin functions to manipulate the servers table.
 * @author maikel
 */
abstract class AdminServers {

	public static function reload() {
		header('Location: admin_servers.php');
		exit;
	}

	public static function resetDb() {
		mysql_query('delete from servers where url != "";');
		include 'net/external_servers.php';
		foreach ($external_servers as $i => $server) {
			$server->dbInsert();
		}
	}

	public static function addUrl($url) {
		if (strlen($url) > 7) {
			mysql_query('insert into servers'
			. ' (name, url) values'
			. ' ("'	. $url . '", "'	. $url.'");');
		}
	}

	public static function menuLinksWhite($url) {
		return self::blacklistLink($url) . self::deleteLink($url);
	}

	public static function menuLinksUnknown($url) {
		return self::activateLink($url) . self::blacklistLink($url) . self::deleteLink($url);
	}

	public static function menuLinksBlack($url) {
		return self::activateLink($url) . self::deleteLink($url);
	}

	private static function blacklistLink($url) {
		return self::actionLink($url, 'blacklist', '&darr;');
	}

	private static function deleteLink($url) {
		return self::actionLink($url, 'delete', 'X');
	}

	private static function activateLink($url) {
		return self::actionLink($url, 'activate', '&uarr;');
	}

	private static function actionLink($url, $action, $symbol) {
		$link = ' [' . $symbol
		. '<a href="admin_servers.php?' . $action . '='
		. $url . '">' . $action . '</a>]';
		return $link;
	}

}

$localServer = new LocalServer();

if (isset($_POST['local_name'])) {
	if ($localServer->isEmpty()) {
		$localServer->setUp($_POST['local_name']);
		AdminServers::resetDb();
	}
	else {
		$localServer->update($_POST['local_name']);
	}
	AdminServers::reload();
}

if (isset($_GET['drop'])) {
	mysql_query('drop table servers;');
	Header('Location: admin.php');
	exit;
}

if (!$localServer->isEmpty()) {

	if (isset($_GET['reset_servers'])) {
		AdminServers::resetDb();
	}

	if (isset($_GET['remember'])) {
		$localServer->setRemembering((bool) $_GET['remember']);
	}

	if (isset($_GET['add_suggested'])) {
		$localServer->setAccepting((bool) $_GET['add_suggested']);
	}

	if (isset($_POST['new_url'])) {
		AdminServers::addUrl($_POST['new_url']);
	}

	if (isset($_GET['blacklist'])) {
		ExternalServer::blacklist($_GET['blacklist']);
	}

	if (isset($_GET['activate'])) {
		ExternalServer::activate($_GET['activate']);
	}

	if (isset($_GET['delete'])) {
		ExternalServer::delete($_GET['delete']);
	}

	$activeServers = ExternalServerPool::whiteServerPool();
	$activeList = '';
	while ($server = $activeServers->next()) {
		$activeList .= '<li>'
		. $server->toHtmlLink()
		. AdminServers::menuLinksWhite($server->getUrl())
		. '</li>';
	}

	$unknownServers = ExternalServerPool::unknownServerPool();
	$unknownList = '';
	while ($server = $unknownServers->next()) {
		$unknownList .= '<li>'
		. $server->toHtmlLink()
		. AdminServers::menuLinksUnknown($server->getUrl())
		. '</li>';
	}

	$blacklistServers = ExternalServerPool::blacklistServerPool();
	$blackList = '';
	while ($server = $blacklistServers->next()) {
		$blackList .= '<li>'
		. $server->toHtmlLink()
		. AdminServers::menuLinksBlack($server->getUrl())
		. '</li>';
	}

}

if (sizeof($_GET)) AdminServers::reload();
if (sizeof($_POST)) AdminServers::reload();

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
<p><a href="admin_servers.php?edit_name=1">Namen des Standorts ändern</a></p>
<p><a href="admin_servers.php?drop=1">Suche an anderen Standorten
ausschalten</a><br />
Dies löscht den Standortnamen und die Liste der Standorte.</p>


<h2>Automatische Standortverwaltung</h2>
<p>Unbekannte Standorte deaktiviert merken: <?php if ($localServer->rememberNewServers()) { ?>
aktiv &harr; <a href="admin_servers.php?remember=0">deaktivieren</a> <?php } else {?>
<a href="admin_servers.php?remember=1">aktivieren</a> &harr; deaktiviert
<?php }?></p>

<p>Empfohlene Standorte von anderen übernehmen: <?php if ($localServer->acceptSuggestedServers()) { ?>
aktiv &harr; <a href="admin_servers.php?add_suggested=0">deaktivieren</a>
<?php } else {?> <a href="admin_servers.php?add_suggested=1">aktivieren</a>
&harr; deaktiviert <?php }?></p>

<h2>Liste aller Standorte</h2>

<h3>Aktive Standorte: <?php echo $activeServers->size(); ?></h3>
<ul class="text">
<?php echo $activeList; ?>
	<li>
	<form action="admin_servers.php" method="post"><input type="text"
		name="new_url" value="http://" class="fullsize" /><input type="submit"
		value="Eintragen" /></form>
	</li>
</ul>

<h3>Unbekannte Standorte: <?php echo $unknownServers->size(); ?></h3>
<ul class="text">
<?php echo $unknownList; ?>
</ul>

<h3>Blacklist: <?php echo $blacklistServers->size(); ?></h3>
<ul class="text">
<?php echo $blackList; ?>
</ul>


<h2>Reset</h2>
<div class="menu"><span><a href="admin_servers.php?reset_servers=1">Alle
Standorteinträge zurücksetzen</a></span></div>

<?php } ?>

<?php require 'footer.php'; ?>
