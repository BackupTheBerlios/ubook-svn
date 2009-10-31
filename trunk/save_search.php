<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'books/SearchKey.php';
require_once 'notification/Searches.php';

$searchKey = new SearchKey();

if (!$searchKey->isGiven()) {
	header('Location: ./');
}

if (isset($_GET['name'])) { // field for mail address
	$searches = new Searches();
	$searches->addSearch($searchKey->asText(), $_GET['name']);
	header('Location: ./?search=' . urlencode($searchKey->asText()) . '&searchSaved=1');
} else {

}

include 'header.php';
?>

<h2>Suche nach '<?php echo $searchKey->asHtml(); ?>'</h2>
<div>
<form action="save_search.php" method="get">
 <input type="hidden" name="search" value="<?php echo $searchKey->asHtml(); ?>" />
 <label>Deine E-Mailadresse:<br />
 <input type="text" name="name" value="" size="20" alt="E-Mailadresse" />
 </label>
 <input type="submit" value="Suche abonnieren" />
</form>
</div>
<p>
&larr; <a href="./?search=<?php echo urlencode($searchKey->asText()); ?>">Zurück zur Suche</a>
</p>

<?php include 'footer.php'; ?>