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