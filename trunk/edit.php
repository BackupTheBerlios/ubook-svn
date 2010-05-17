<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

// generates output with select fields
function assignSelectableCategories($selectableCategories, Template $tmpl) {
	$selCatArray = $selectableCategories->createSelectArray();
	if (sizeOf($selCatArray) < 1) return;
    $categoriesTmpl = $tmpl->addSubtemplate('categories');
    $categoriesTmpl->assign('category0', $selCatArray[0]);
	for ($i = 1; $i < sizeOf($selCatArray); $i++) {
        $catTmpl = $categoriesTmpl->addSubtemplate('category');
        $catTmpl->assign('category', $selCatArray[$i]);
	}
}

require_once 'mysql_conn.php';
require_once 'tools/BookFetcher.php';
require_once 'tools/SelectableCategories.php';
require_once 'tools/Parser.php';

if (!isset($_GET['id'])) exit;
if (!isset($_GET['key'])) exit;

$id = (int) $_GET['id'];
$key = $_GET['key'];

$query = 'select
 author, title, year, price, expires, description
 from books where id="'.$id.'" and auth_key="'.$key.'"';
$result = mysql_query($query);
if (mysql_num_rows($result) == 0) {
	$error = 'not found';
} else {
	/* we have valid access to this book */
	$selectableCategories = new SelectableCategories($id);

	if (isset($_POST['author'])) {
		/* update base book data */
		$query = 'update books set
  		author = "'.$_POST['author'].'",
  		title = "'.$_POST['title'].'",
  		year = "'.$_POST['year'].'",
  		price = "'.str_replace(',','.',$_POST['price']).'",
  		description = "'.$_POST['description'].'"
	     where id="'.$id.'" and auth_key="'.$key.'"';
		mysql_query($query);
		/* update category relations */
		$selectableCategories->update();
		/* update expire date and look at the book */
		require 'renew.php';
	}

	$book = BookFetcher::fetchHtml($result);

    require_once 'tools/Output.php';
    require_once 'tools/Template.php';
    $tmpl = Template::fromFile('view/edit.html');
    $tmpl->assign('id', $_GET['id']);
    $tmpl->assign('key', $_GET['key']);
    foreach ($book as $name => $value) {
        $tmpl->assign($name, $value);
    }
    assignSelectableCategories($selectableCategories, $tmpl);
    $output = new Output($tmpl->result());
    $output->send();
}

?>
