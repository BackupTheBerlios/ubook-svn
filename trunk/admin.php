<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

require_once 'books/AbstractBookList.php';
$numberOfBooks = AbstractBookList::numberOfAllBooks();

require_once 'Categories.php';
$categories = new Categories();
$numberOfCategories = sizeof($categories->getArray());

require_once 'LocalServerName.php';
$serverName = new LocalServerName();

require_once 'books/ExternalServerPool.php';
$serverPool = new ExternalServerPool(true, true);
$numberOfServers = $serverPool->size();

/*
 * Optionen anbieten:
 * MySQL-Setup, Bücher betrachten, Kategorien verändern, Serveraustausch aktivieren, Mail verschicken.
 */
require 'header.php';

?>
<h2>uBook läuft</h2>
<table align="center">
<tr><td>Buchangebote</td><td><?php echo $numberOfBooks; ?></td><td><a href="admin_view.php">alle einsehen</a></td></tr>
<tr><td>Kategorien</td><td><?php echo $numberOfCategories; ?></td><td><a href="admin_categories.php">verwalten</a></td></tr>
<?php if ($serverName->isEmpty()) { ?>
<tr><td colspan="3"><a href="admin_servers.php">Suche an anderen Standorten aktivieren</a></td></tr>
<?php } else { ?>
<tr><td>Bekannte Standorte</td><td><?php echo $numberOfServers; ?></td><td><a href="admin_servers.php">einsehen</a></td></tr>
<?php } ?>
<tr><td colspan="3"><a href="admin_mail.php">E-Mail an alle Mailadressen versenden</a></td></tr>
</table>

<?php require 'footer.php'; ?>
