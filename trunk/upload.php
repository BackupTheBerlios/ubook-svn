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
require_once 'tools/Image.php';
require_once 'tools/Output.php';
require_once 'text/Template.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

$query = 'select id from books where id="'.$id.'" and auth_key="'.$key.'";';
$result = mysql_query($query);

if (mysql_num_rows($result) != 1) exit;
// now we have valid access

$output = new Output();

$tmpl = Template::fromFile('view/upload.html');
$tmpl->assign('id', $id);
$tmpl->assign('key', $key);

if (isset($_GET['upload'])) {
	if (Image::wasUploaded()) {
		if (Image::isStorable()) {
			$image = new Image($id);
			if ($image->moveUploaded()) {
				header('Location: book.php?id='.$id.'&key='.$key.'&uploaded=true');
			} else {
				$tmpl->addSubtemplate('processingError');
			}
		} else {
			$tmpl->addSubtemplate('notStorable');
		}
	} else {
		$tmpl->addSubtemplate('unknownError');
	}
} else {
	if (isset($_GET['delete'])) {
		$delete = (bool) $_GET['delete'];
		if ($delete == true) {
			$image = new Image($id);
			$image->delete();
			$tmpl->addSubtemplate('deleted');
		} else {
			$tmpl->assign('imgTag', Image::imgTag($id));
			$tmpl->addSubtemplate('deleteConfirmation');
			$output->send($tmpl->result());
			exit;
		}
	} else {
		if (isset($_FILES['image'])) {
			$tmpl->addSubtemplate('uploadError');
		}
	}
}

$tmpl->assign('imgTag', Image::imgTag($id));
$tmpl->addSubtemplate('forms');
$output->send($tmpl->result());
?>
