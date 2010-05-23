<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/
/*
 * TODO and further ideas
 * A collection of ideas, what should or could be done in the future.
 *
 * - new column in the book table: isbn
 * - upload.php: error-reporting for the user
 * - mirgration from XHTML 1.0 to 1.1
 * - Sending HTTP Not Changed answers (template engine?)
 * - feedback, if a wrong mail address was typed in
 * - statistics:
 *   - percentage of books with image
 *   - distribution of books in the time (created, expire, lifetime)
 *   - books per person
 *   - number of stored mail addresses
 *   - active books / total number of books (with deleted ones)
 * - (Bielefeld) category 'Sprachbücher', because of a lot of language lectures
 * - (Bielefeld) categories of the FH
 * - new category schema, perhaps a tree?
 * - Provide more than HTML and XML for the book search results:
 *   - JASON, JAML, RDF(S)
*/
if (!is_readable('mysql.php')) header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'books/SearchKey.php';
require_once 'books/BookList.php';
require_once 'tools/Categories.php';
require_once 'tools/Output.php';
require_once 'tools/Template.php';

class indexPage {

    private $output;
    private $tmpl;

    public function __construct() {
        $this->output = new Output();
        $this->tmpl = Template::fromFile('view/index.html');
        $this->basicOutput();
    }

    public function setStatic() {
        $this->output->setExpires(43200);
        $this->tmpl->assign('htmlSearchKey', '');
    }

    public function setDynamic() {
        $this->output->addNavigationLink('first', 'Erste', './');
    }

    public function displayMessages() {
        if (isset($_GET['searchSaved'])) {
            $this->tmpl->addSubtemplate('searchSaved');
        }
        elseif (isset($_GET['searchDeleted']) && $_GET['searchDeleted']) {
            $this->tmpl->addSubtemplate('searchDeleted');
        }
    }

    public function search() {
        $searchKey = new SearchKey();
        $this->tmpl->assign('htmlSearchKey', $searchKey->asHtml());
        if (!$searchKey->isGiven()) return;
        $searchKey->getOption();
        if ($searchKey->getOption() != 'random') {
            $this->createFeedLink($searchKey);
            $this->createSaveLink();
        }
        require_once 'books/SearchKeyBookList.php';
        $bookList = new SearchKeyBookList($searchKey);
        if ($bookList->size() == 0) {
            /* Nothing found here, ask other servers. */
            $this->externalSearch($searchKey);
            return;
        }
        $results = $this->tmpl->addSubtemplate('searchResults');
        $results->assign('HtmlRows', $bookList->toHtmlRows());
        $results->assign('found', $bookList->size());
        $results->assign('numberOfAllBooks', AbstractBookList::numberOfAllBooks());
    }

    public function displayCategories() {
        $categories = new Categories();
        $cat_arr = $categories->getArray();
        $menu =	'<div class="categories" style="width:30em; margin:0px auto; margin-top:1em;">Kategorien: ';
        foreach ($cat_arr as $index => $category) {
            $menu .= '<span><a href="?cat=';
            $menu .= $category;
            $menu .= '">';
            $menu .= $category;
            $menu .= '</a></span> ';
        }
        $menu .= '</div>';
        $this->tmpl->assign('categoryMenu', $menu);
    }

    public function searchCategory() {
        if (!isset($_GET['cat'])) return;
        require_once 'books/CategoryBookList.php';
        $category = trim($_GET['cat']);
        $catTmpl = $this->tmpl->addSubtemplate('categorySearch');
        $catBookList = new CategoryBookList($category);
        if ($catBookList->size()) {
            $results = $catTmpl->addSubtemplate('categoryResults');
            $results->assign('HtmlRows', $catBookList->toHtmlRows());
            $results->assign('found', $catBookList->size());
            $results->assign('numberOfAllBooks', AbstractBookList::numberOfAllBooks());
        } else {
            $catTmpl->addSubtemplate('noCategoryResults');
        }
    }

    public function send() {
        $this->output->send($this->tmpl->result());
    }

    private function basicOutput() {
        $this->output->unlinkMenuEntry('Buch suchen');
        $this->output->setFocus('search');
        $this->displayCategories();
    }

    private function createFeedLink(SearchKey $searchKey) {
        $feedUrl = WEBDIR . 'rss.php?search=' . urlencode($searchKey->asText());
        $this->output->addFeedLink($feedUrl);
        $link = $this->tmpl->addSubtemplate('searchAsRss');
        $link->assign('feedUrl', $feedUrl);
    }

    private function createSaveLink() {
        if (isset($_GET['searchSaved'])) return;
        require_once 'notification/Searches.php';
        $searches = new Searches();
        if (!$searches->areActivated()) return;
        $this->tmpl->addSubtemplate('saveSearch');
    }

    private function externalSearch(SearchKey $searchKey) {
        require_once 'net/ExternalBookList.php';
        require_once 'net/ExternalServerPool.php';
        require_once 'net/ThreadedBookListReader.php';
        $serverPool = ExternalServerPool::activeServerPool();
        $reader = new ThreadedBookListReader($serverPool, $searchKey);
        $externalBookListArray = $reader->read();
        if (sizeof($externalBookListArray) == 0) {
            $this->tmpl->addSubtemplate('noResults');
            return;
        }
        $external = $this->tmpl->addSubtemplate('externalSearch');
        foreach ($externalBookListArray as $i => $externalBookList) {
            $set = $external->addSubtemplate('externalResultSet');
            $set->assign('locationName', $externalBookList->locationName());
            $set->assign('HtmlRows', $externalBookList->toHtmlRows());
        }
    }
}

$indexPage = new indexPage();

/* basic variables */
$category = '';
$searchKey = new SearchKey();


if (sizeof($_GET) == 0) {
    $indexPage->setStatic();
}
else {
    /* Okay, dealing user input */
    $indexPage->setDynamic();
    /* Cleaning old books before searching */
    require_once 'books/Cleaner.php';
    Cleaner::checkOld();

    $indexPage->displayMessages();
    $indexPage->search();
    $indexPage->searchCategory();

}

$indexPage->send();
?>
