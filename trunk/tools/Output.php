<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'Template.php';

/**
 * Represents the output sent to the browser.
 */
class Output {

    private $template;
    private $expires = 0;
    private $menuEntries;

    public function __construct() {
        $this->template = Template::fromFile('view/page.html');
        $this->menuEntries = array(
            'Buch suchen' => './',
            'Buch anbieten' => 'add.php',
            'Tipps' => 'help.php',
            'Impressum' => 'about.php'
        );
    }

    public function setExpires($expires) {
        $this->expires = $expires;
    }

    /**
     * Adds navigation links to the HTML head.
     * @param string $rel one of 'next', 'prev', 'first' and 'last'
     * @param string $title to display the user
     * @param string $href an URL
     */
    public function addNavigationLink($rel, $title, $href) {
        $sub = $this->template->addSubtemplate('navigation');
        $sub->assign('rel', $rel);
        $sub->assign('title', $title);
        $sub->assign('href', $href);
    }

    /**
     * Adds a refresh rule. The browser should load the given reference after
     * some seconds.
     * @param string $href an URL
     */
    public function addRefreshLink($href) {
        $sub = $this->template->addSubtemplate('refresh');
        $sub->assign('url', $href);
    }

    public function unlinkMenuEntry($label) {
        $this->menuEntries[$label] = '';
    }

    public function setFocus($elementId) {
        $focus = $this->template->addSubtemplate('focus');
        $focus->assign('elementWithFocus', $elementId);
    }

    public function addFeedLink($feedUrl) {
        $feedLink = $this->template->addSubtemplate('feedLink');
        $feedLink->assign('feedUrl', $feedUrl);
    }

    public function send($content) {
        $this->configureTemplate();
        $this->template->assign('content', $content);
        if (isset($_SERVER['SERVER_NAME'])) {
            header('Content-Type: text/html;charset=utf-8');
        }
        echo $this->template->result();
    }

    public function sendNotFound($content = null) {
        header('HTTP/1.0 404 Not Found');
        if ($content === null) {
            $tmpl = Template::fromFile('view/NotFound.html');
            $content = $tmpl->result();
        }
        $this->send($content);
    }

    private function configureTemplate() {
        $t = $this->template;
        if ($this->expires) {
            $expTemp = $t->addSubtemplate('expires');
            $expTemp->assign('expires', $this->expires);
        } else {
            $t->addSubtemplate('expiresNow');
        }
        foreach ($this->menuEntries as $label => $url) {
            $entry = $this->template->addSubtemplate('menuEntry');
            if ($url) {
                $entryContent = $entry->addSubtemplate('linkedMenuEntry');
                $entryContent->assign('url', $url);
            } else {
                $entryContent = $entry->addSubtemplate('unlinkedMenuEntry');
            }
            $entryContent->assign('label', $label);
        }
    }

}

?>
