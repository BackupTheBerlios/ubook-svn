<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
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

require_once 'books/SearchKey.php';
require_once 'notification/Searches.php';
require_once 'tools/Mailer.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) {
    header('Location: ./');
}

$mail = Mailer::mailFromUser('mail');
if ($mail == null) {
    $mail = Mailer::mailFromUser('name');
}

if ($mail) {
    $searches = new Searches();
    $searches->addSearch($searchKey->asText(), $mail);
    header('Location: ./?search=' . urlencode($searchKey->asText()) . '&searchSaved=1');
}

$tmpl = Template::fromFile("view/save_search.html");
$tmpl->assign('searchKey', $searchKey->asHtml());
$tmpl->assign('urlSearchKey', urlencode($searchKey->asHtml()));

$output = new Output();
$output->send($tmpl->result());
?>
