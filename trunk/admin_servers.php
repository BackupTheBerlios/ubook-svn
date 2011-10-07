<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
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

require_once 'mysql_conn.php';
require_once 'net/ExternalServer.php';
require_once 'net/ExternalServerPool.php';
require_once 'net/LocalServer.php';
require_once 'tools/Output.php';
require_once 'text/Template.php';

/**
 * Provides admin functions to manipulate the servers table.
 * @author maikel
 */
abstract class AdminServers {

    public static $editName = false;

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
                    . ' ("' . $url . '", "' . $url . '");');
        }
    }

    public static function menuLinksUnknown($url) {
        return self::activateLink($url) . self::blacklistLink($url) . self::deleteLink($url);
    }

    public static function menuLinksBlack($url) {
        return self::activateLink($url) . self::deleteLink($url);
    }

}

$localServer = new LocalServer();

if (isset($_POST['local_name'])) {
    if ($localServer->isEmpty()) {
        $localServer->setUp($_POST['local_name']);
        AdminServers::resetDb();
    } else {
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

    if (isset($_GET['edit_name'])) {
        AdminServers::$editName = true;
        unset($_GET['edit_name']);
    }

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

    if (isset($_POST['group'])) {
        ExternalServer::changeGroup($_POST['url'], $_POST['group']);
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
}

if (sizeof($_GET))
    AdminServers::reload();
if (sizeof($_POST))
    AdminServers::reload();

$output = new Output();
$template = Template::fromFile('view/admin_servers.html');

$template->assign('localServerName', $localServer->name());
if ($localServer->isEmpty() || AdminServers::$editName) {
    $template->addSubtemplate('defineName');
} else {
    $sub = $template->addSubtemplate('listServers');
    if ($localServer->rememberNewServers()) {
        $sub->addSubtemplate('rememberingServers');
    } else {
        $sub->addSubtemplate('notRememberingServers');
    }
    if ($localServer->acceptSuggestedServers()) {
        $sub->addSubtemplate('acceptingSuggestedServers');
    } else {
        $sub->addSubtemplate('notAcceptingSuggestedServers');
    }

    $activeServers = ExternalServerPool::whiteServerPool();
    $sub->assign('numOfActive', $activeServers->size());
    foreach ($activeServers->toArray() as $server) {
        $a = $sub->addSubtemplate('activeListEntry');
        $a->assign('url', $server->getUrl());
        $a->assign('name', $server->getName());
        $a->assign('group', $server->getDistanceGroup());
    }
    $unknownServers = ExternalServerPool::unknownServerPool();
    $sub->assign('numOfUnknown', $unknownServers->size());
    foreach ($unknownServers->toArray() as $server) {
        $a = $sub->addSubtemplate('unknownListEntry');
        $a->assign('url', $server->getUrl());
        $a->assign('name', $server->getName());
    }
    $blacklistServers = ExternalServerPool::blacklistServerPool();
    $sub->assign('numOfBlacklisted', $blacklistServers->size());
    foreach ($blacklistServers->toArray() as $server) {
        $a = $sub->addSubtemplate('blackListEntry');
        $a->assign('url', $server->getUrl());
        $a->assign('name', $server->getName());
    }
}

$output->send($template->result());
?>