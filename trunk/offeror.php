<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

if (!isset($_GET['id'])) exit;

$id = (int) $_GET['id'];

require_once 'books/OfferorsBookList.php';

$bookList = new OfferorsBookList($id);

require 'header.php';

?>

<div class="menu">
   <span><a href="./">Buch suchen</a></span>
   <span><a href="add.php">Buch anbieten</a></span>
   <span><a href="help.php">Tipps</a></span>
   <span><a href="about.php">Impressum</a></span>
</div>
<h2>Bücher einer Anbieterin / eines Anbieters</h2>
<div class="results">
<table align="center">
<?php echo $bookList->toHtmlRows(); ?>
</table>
</div>

<?php include 'footer.php'; ?>