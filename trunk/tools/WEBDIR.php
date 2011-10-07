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

/**
 * Defines the constant WEBDIR. Normally it consists of the server name and
 * directory. Executed locally, it will be a local file path.
 */
function define_webdir() {
    if (isset($_SERVER['SERVER_NAME'])) {
        $protocol = 'http://';
        $uri = $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
    } else {
        $protocol = 'file://';
        $uri = $_ENV['PWD'] . '/';
    }
    define('WEBDIR', $protocol . str_replace('//', '/', $uri));
}

define_webdir();
?>
