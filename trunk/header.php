<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

/**
 * Prints navigation links defined in the global var $navigation_links.
 * You can define 'next', 'prev', 'first' and 'last'.
 */
function echo_navigation() {
	if (!isset($GLOBALS['navigation_links'])) return;
	$navLinks = $GLOBALS['navigation_links'];
	foreach ($navLinks as $rel => $title_link) {
		$title = $title_link[0];
		$link = $title_link[1];
		echo '<link rel="'.$rel.'" title="'.$title.'" href="'.$link.'" />';
	}
}

header('Content-Type: text/html;charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <?php if (isset($http_equiv_expires)) { ?>
  <meta http-equiv="expires" content="<? echo $http_equiv_expires; ?>" />
  <?php } else { ?>
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="pragma" content="no-cache" />
  <?php } ?>
  <meta http-equiv="content-language" content="de" />
  <meta name="description" content="uBook - a website to buy and sell books" />
  <meta name="keywords" content="uBook, u, book, u-book, books, buy, sell, market, exchange, free, Buch, Bücher, Börse, Bücherbörse, Buchbörse, Verkaufen, anbieten, kaufen, AStA, Bielefeld" />
  <meta name="robots" content="index,follow" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <link rel="shortcut icon" type="image/x-icon" href="uBook_icon.png" />
  <link rel="author" title="Impressum" href="about.php" />
  <link rel="index" title="Kategorien" href="./" />
  <link rel="search" title="Suche" href="./" />
  <link rel="help" title="Tipps" href="help.php" />
  <link rel="copyright" title="Urheberrecht" href="COPYING" />
  <link rel="top" title="Startseite" href="./" />
  <link rel="up" title="Hoch" href="./" />
<?php echo_navigation(); ?>
<?php if (isset($feedLink)) echo $feedLink; ?>
  <title>uBook</title>
 </head>
 <body>
  <div class="head">
  <a href="./">
   <img src="ubook_small.gif" border="0" alt="uBook-Logo" />
  </a>
  </div>
