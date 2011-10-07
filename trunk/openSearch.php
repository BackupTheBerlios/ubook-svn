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

require_once 'text/Template.php';
require_once 'tools/WEBDIR.php';

$tmpl = Template::fromFile('view/openSearch.xml');
$tmpl->assign('siteurl', WEBDIR);

header('Content-Type: application/opensearchdescription+xml;charset=utf-8');
//header('Content-Type: text/xml;charset=utf-8');
echo $tmpl->result();
?>