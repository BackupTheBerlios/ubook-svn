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
if (!is_readable('mysql.php'))
    header('Location: ./admin_setup.php');

include_once 'magic_quotes.php';
require_once 'books/Cleaner.php';
require_once 'books/LocalSearchBookList.php';
require_once 'books/SearchKey.php';
require_once 'net/ExternalBookList.php';
require_once 'net/ExternalBookListReader.php';
require_once 'net/ExternalServerPool.php';
require_once 'notification/Searches.php';
require_once 'tools/Categories.php';
require_once 'tools/Output.php';
require_once 'tools/Statistics.php';
require_once 'text/Template.php';

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
        } elseif (isset($_GET['searchDeleted']) && $_GET['searchDeleted']) {
            $this->tmpl->addSubtemplate('searchDeleted');
        }
    }

    public function search() {
        $searchKey = new SearchKey();
        $this->tmpl->assign('htmlSearchKey', $searchKey->asHtml());
        if (!$searchKey->isGiven())
            return;
        if ($searchKey->getOption() != 'random') {
            $this->createFeedLink($searchKey);
            $this->createSaveLink();
        }
        $serverPool = ExternalServerPool::activeServerPool();
        /*
         * Important order! ExternalBookListReader starts HTTP requests in the
         * constructor. The local database can be read while the external
         * servers are busy. Calling read(0) will read and parse the answers of
         * the servers.
         */
        $externalReader = new ExternalBookListReader($serverPool, $searchKey);
        $localBookList = new LocalSearchBookList($searchKey);
        $nearbyBookListArray = $externalReader->readNextGroup(0);
        if ($localBookList->size() || sizeof($nearbyBookListArray)) {
            $results = $this->tmpl->addSubtemplate('searchResults');
            $results->assign('HtmlRows', $localBookList->toHtmlRows());
            foreach ($nearbyBookListArray as $nearbyBookList) {
                $set = $results->addSubtemplate('nearbyResultSet');
                $set->assign('locationName', $nearbyBookList->locationName());
                $set->assign('HtmlRows', $nearbyBookList->toHtmlRows());
            }
        } else {
            /* Nothing found here, ask other servers group by group. */
            $externalBookListArray = $externalReader->readNextGroup(255);
            if (sizeof($externalBookListArray) == 0) {
                $this->tmpl->addSubtemplate('noResults');
                return;
            }
            $external = $this->tmpl->addSubtemplate('externalSearch');
            foreach ($externalBookListArray as $externalBookList) {
                $set = $external->addSubtemplate('externalResultSet');
                $set->assign('locationName', $externalBookList->locationName());
                $set->assign('HtmlRows', $externalBookList->toHtmlRows());
            }
        }
    }

    public function displayCategories() {
        $categories = new Categories();
        $cat_arr = $categories->getArray();
        $menu = '<div class="categories" style="width:30em; margin:0px auto; margin-top:1em;">Kategorien: ';
        foreach ($cat_arr as $index => $category) {
            $menu .= '<span><a href="?cat=';
            $menu .= urlencode($category);
            $menu .= '">';
            $menu .= $category;
            $menu .= '</a></span> ';
        }
        $menu .= '</div>';
        $this->tmpl->assign('categoryMenu', $menu);
    }

    public function searchCategory() {
        if (!isset($_GET['cat']))
            return;
        require_once 'books/CategoryBookList.php';
        $category = trim($_GET['cat']);
        $catTmpl = $this->tmpl->addSubtemplate('categorySearch');
        $catTmpl->assign('category', $category);
        $catBookList = new CategoryBookList($category);
        if ($catBookList->size()) {
            $results = $catTmpl->addSubtemplate('categoryResults');
            $results->assign('HtmlRows', $catBookList->toHtmlRows());
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
        if (isset($_GET['searchSaved']))
            return;
        $searches = new Searches();
        if (!$searches->areActivated())
            return;
        $this->tmpl->addSubtemplate('saveSearch');
    }

}

$indexPage = new indexPage();

/* basic variables */
$category = '';
$searchKey = new SearchKey();


if (sizeof($_GET) == 0) {
    $indexPage->setStatic();
} else {
    /* Okay, dealing user input */
    $indexPage->setDynamic();
    /* Cleaning old books before searching */
    Cleaner::checkOld();
    /* Log statistics */
    $statistics = new Statistics();
    $statistics->writeStats();

    $indexPage->displayMessages();
    $indexPage->search();
    $indexPage->searchCategory();
}

$indexPage->send();
?>
