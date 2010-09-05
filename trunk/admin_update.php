<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'update/Updater.php';
require_once 'tools/Output.php';
require_once 'tools/Template.php';

$output = new Output();
$tmpl = Template::fromFile('view/update.html');

$updater = new Updater();

if ($updater->hasWork()) {
    $updater->update();
    $sub = $tmpl->addSubtemplate('working');
    $sub->assign('version', $updater->getNextVersion());
    $output->addRefreshLink('./admin_update.php');
} else {
    $tmpl->addSubtemplate('noWork');
}

$output->send($tmpl->result());

?>