<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Image.php';
require_once 'tools/Statistics.php';

$books = '';
$error = '';

if (!is_file('mysql.php')) { // no config file
    if (!is_writable('./'))
        $error = 'not writeable';
    else {
        if (isset($_POST['server'])) {
            $fp = fopen('mysql.php', 'w');
            $filedata = '<?php $server = \'' . $_POST['server'] . '\'; $username = \'' . $_POST['username'] . '\'; $password = \'' . $_POST['password'] . '\'; $database = \'' . $_POST['database'] . '\'; ?>';
            fwrite($fp, $filedata, strlen($filedata));
            fclose($fp);
            chmod('mysql.php', 0400);
            mkdir(Image::PATH, 0755);
            mkdir(Statistics::STATS_DIR, 0755);
            $error = '';
        } else {
            $error = 'no file';
        }
    }
}

if ($error == '') {
    require_once 'tools/MyDatabase.php';
    MyDatabase::connect();
    $table_books = mysql_query('describe books;');
    if ($table_books == null) { // no table, so create
        $new_table = 'CREATE TABLE `books` (';
        $new_table .= '`id` bigint(20) NOT NULL auto_increment,';
        $new_table .= '`auth_key` varchar(32) NOT NULL,';
        $new_table .= '`mail` varchar(128) NOT NULL,';
        $new_table .= '`created` datetime NOT NULL,';
        $new_table .= '`expires` datetime NOT NULL,';
        $new_table .= '`expired` datetime,';
        $new_table .= '`author` varchar(128) NOT NULL,';
        $new_table .= '`title` varchar(128) NOT NULL,';
        $new_table .= '`year` decimal(4,0) NOT NULL,';
        $new_table .= '`price` decimal(5,2) NOT NULL,';
        $new_table .= '`isbn` varchar(17) NOT NULL,';
        $new_table .= '`description` text NOT NULL,';
        $new_table .= 'PRIMARY KEY  (`id`)';
        $new_table .= ')';
        mysql_query($new_table);
    }
    $table_categories = mysql_query('describe categories;');
    if ($table_categories == null) { // no table, so create
        $new_table = 'CREATE TABLE `categories` (';
        $new_table .= '`category` varchar(32) NOT NULL,';
        $new_table .= 'PRIMARY KEY  (`category`)';
        $new_table .= ')';
        mysql_query($new_table);
    }
    $table_book_cat_rel = mysql_query('describe book_cat_rel;');
    if ($table_book_cat_rel == null) { // no table, so create
        $new_table = 'CREATE TABLE `book_cat_rel` (';
        $new_table .= '`book_id` bigint(20) NOT NULL,';
        $new_table .= '`category` varchar(32) NOT NULL,';
        $new_table .= 'PRIMARY KEY  (`book_id`,`category`)';
        $new_table .= ')';
        mysql_query($new_table);
    }
}

require 'header.php';
?>

<?php if ($error == '') {
 ?>
    <div class="infobox">Das Setup ist fertig.</div>
    <div class="menu">
        <span><a href="admin.php">Zur Administrationsübersicht &rarr;</a></span>
    </div>
<?php } ?>


<?php if ($error == 'not writeable') { ?>
    <p>In diesem Verzeichnis muss die Konfigurationsdatei "mysql.php" geschrieben und ein Verzeichnis zum Bilderupload erstellt werden. Dazu braucht der Webserver das Schreibrecht für dieses Verzeichnis. Vergib das Schreibrecht und es geht weiter.</p>
    <form action="admin_setup.php" method="get"><p><input type="submit" value="Weiter" /></p></form>
<?php } ?>

<?php if ($error == 'no file') { ?>
    <p>Dieses Programm braucht Zugang zu einer MySQL-Datenbank.</p>
    <form action="admin_setup.php" method="post">
        <table align="center" style="width:35em;">
            <tr><td>MySQL-Server</td><td><input type="text" name="server" value="localhost" /></td></tr>
            <tr><td>Benutzername</td><td><input type="text" name="username" value="ubook" /></td></tr>
            <tr><td>Passwort</td><td><input type="password" name="password" value="" /></td></tr>
            <tr><td>Datenbank</td><td><input type="text" name="database" value="ubook" /></td></tr>
        </table>
        <p><input type="submit" value="Weiter" /></p>
    </form>
<?php } ?>

<?php include 'footer.php'; ?>
