<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'Template.php';

/*
 * TODO: check more header functionality from:
 * http://php.net/manual/de/function.header.php
 */

/*
 * Represents the output sent to the browser.
*/
class Output {

    private $template;
    private $expires = 0;
    private $navigationLinks = array();

    public function  __construct($content) {
        $tmpl = Template::fromFile('view/page.html');
        $tmpl->assign('content', $content);
        $this->template = $tmpl;
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

    public function send() {
        $this->configureTemplate();
        header('Content-Type: text/html;charset=utf-8');
        echo $this->template->result();
    }

    public function sendNotFound() {
        header('HTTP/1.0 404 Not Found');
        $this->send();
    }

    private function configureTemplate() {
        $t = $this->template;
        if ($this->expires) {
            $expTemp = $t->addSubtemplate('expires');
            $expTemp->assign('expires', $this->expires);
        } else {
            $t->addSubtemplate('expiresNow');
        }
    }
}
?>
