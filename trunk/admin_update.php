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

require_once 'update/Updater.php';
require_once 'tools/Output.php';
require_once 'text/Template.php';

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