<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'magic_quotes.php';
require_once 'tools/SelectableCategories.php';

// generates output with select fields
function echoSelectableCategories($selectableCategories) {
    $selCatArray = $selectableCategories->createSelectArray();
    foreach ($selCatArray as $i => $selCat) {
        echo ' '.$selCat;
    }
}

function import_book($bookString) {
    $labels = array('Autor', 'Titel', 'Preis', 'Erscheinungsjahr', 'Beschreibung');
    $indices = array('author', 'title', 'price', 'year', 'description');
    $book = array();
    $bookString = trim($bookString);
    $bookLines = split("\n", $bookString, sizeof($labels));
    if (sizeof($bookLines) < sizeof($labels)) return;
    for ($i = 0; $i < sizeof($labels); $i++) {
        list($label, $value) = split(':', $bookLines[$i], 2);
        if (trim($label) != $labels[$i]) return;
        $book[$indices[$i]] = trim($value);
    }
    return $book;
}

$selectableCategories = new SelectableCategories();

$book = array(
        'author' => '',
        'title' => '',
        'price' => '',
        'year' => '',
        'mail' => '',
        'description'
);

if (isset($_POST['book_data'])) {
    $importedBook = import_book($_POST['book_data']);
    if ($importedBook) {
        $book = $importedBook;
    }
}

if (isset($_POST['mail'])) {
    $book['mail'] = $_POST['mail'];
}


include 'header.php';
?>
<div class="menu">
    <span><a href="./">Buch suchen</a></span>
    <span><a href="add.php">Buch anbieten</a></span>
    <span><a href="help.php">Tipps</a></span>
    <span><a href="about.php">Impressum</a></span>
</div>

<?php
if (isset($_POST['book_data'])) { ?>
<fieldset class="fullsize"><legend>Buch anbieten...&nbsp;</legend>
    <form action="add.php" method="post" name="add_form">
        <input type="text" name="name" value="" class="boogy" />
        <label>Nachname, Vorname der Autorin / des Autor<br />
            <input type="text" name="mail" value="<?php echo $book['author']; ?>" class="fullsize" /> </label> <label>Titel
            des Buches<br />
            <input type="text" name="title" value="<?php echo $book['title']; ?>" class="fullsize" /> </label>

        <div style="float: left; margin-right: 2em;"><label>Erscheinungsjahr<br />
                <input type="text" name="year" value="<?php echo $book['year']; ?>" size="6" /> </label></div>

        <div style="margin-bottom: 0.5em;"><label>Dein Preis<br />
                <input type="text" name="price" value="<?php echo $book['price']; ?>" size="6" /> &euro;</label></div>

        <label style="clear: both;">Kategorien<br />
                <?php echoSelectableCategories($selectableCategories); ?></label> <label
            style="clear: both;">Deine E-Mailadresse<br />
            <input type="text" name="author" value="<?php echo $book['mail']; ?>" class="fullsize" /></label> <label>Weiteres<br />
            <textarea name="description" cols="24" rows="10" class="fullsize"><?php echo $book['description']; ?></textarea>
        </label> <br />
        <input type="submit" value="Anbieten" /></form>
    <form action="./" method="get"><input type="submit" value="Abbrechen" />
    </form>
</fieldset>
    <?php } else { ?>
<fieldset class="fullsize"><legend>Buch importieren...&nbsp;</legend>
    <p class="text">
        So importierst du ein gelöschtes Buch einfach wieder:
    </p>
    <ol class="text">
        <li>
            Aus der E-Mail den Text-Block mit der Buchbeschreibung kopieren: Von
            'Autor: ...' bis zum letzten Wort deiner Beschreibung.
        </li>
        <li>Diesen Text in das Textfeld unten einfügen.</li>
        <li>Auf importieren klicken.</li>
    </ol>
    <form action="import.php" method="post">
            <?php if (isset($_GET['mail'])) {
                echo '<input type="hidden" name="mail" value="' . $_GET['mail'] . '" />';
            } ?>
        <label>Importfeld<br />
            <textarea name="book_data" cols="30" rows="20" class="text">
            </textarea>
        </label> <br />
        <input type="submit" value="Importieren" /></form>
    <form action="./" method="get"><input type="submit" value="Abbrechen" />
    </form>
</fieldset>
    <?php } ?>
<?php include 'footer.php'; ?>