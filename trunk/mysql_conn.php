<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
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

/*
 * This script connects to the database or exits by sending an error message.
 * If a connection is etablished, old database entries will be deleted.
 */

require_once 'tools/MyDatabase.php';
require_once 'tools/Output.php';
require_once 'text/Template.php';

$templateText = <<<EOT
    <h2>Störung</h2>
    <div class="text">
    Leider besteht zur Zeit keine Verbindung zur Datenbank. :-(
    </div>
EOT;

MyDatabase::connect();

if (!MyDatabase::getConnection()) {
    $tmpl = new Template($templateText);
    $output = new Output();
    $output->send($tmpl->result());
    exit;
}
?>