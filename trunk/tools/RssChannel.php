<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

/*
 * Represents an RSS channel and produces the correct XML output.
 */

class RssChannel {

    private $doc;
    private $channel;
    private $newest = 0;

    /**
     * Creates an empty news feed.
     * @param string $title title of the feed
     * @param string $link link to the website
     * @param string $desc description
     * @param string $lang language, e.g. de-de
     * @param string $copyright author of the feed
     */
    public function __construct($title, $link, $desc, $lang, $copyright) {
        $this->doc = $this->createRssDoc();
        $channel = $this->createChannel($title, $link, $desc, $lang, $copyright);
        $this->channel = $channel;
        $this->doc->documentElement->appendChild($channel);
    }

    /**
     * Adds an image (logo) to the news feed.
     * @param string $url URL of the image
     * @param string $title title of the image
     * @param string $link link to website
     */
    public function addImage($url, $title, $link) {
        $this->addToChannel($this->createImage($url, $title, $link));
    }

    /**
     * Adds a news item to the feed.
     * @param string $id a unique identifyer of this item
     * @param string $title title of the news item
     * @param string $desc description of the news item
     * @param string $link link to a longer text
     * @param string $author author of the text
     * @param string $date for example '2010-04-26 15:26:16'
     */
    public function addItem($id, $title, $desc, $link, $author, $date) {
        $this->addToChannel($this->createItem($id, $title, $desc, $link, $author, $date));
    }

    /**
     * Sends the XML output to the user.
     */
    public function send() {
        $xml = $this->doc->saveXML();
        $etag = md5($xml);
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $this->newest) . " GMT");
        header("Etag: $etag");
        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $this->newest
                || trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
        header('Content-Type: text/xml; charset=utf-8');
        echo $xml;
    }

    private function createRssDoc() {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $style = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="RssStyle.xsl"');
        $doc->appendChild($style);
        $rss = $doc->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $doc->appendChild($rss);
        return $doc;
    }

    private function createElement($tagName, $value = '') {
        $value = str_replace('&', '&amp;', $value);
        return $this->doc->createElement($tagName, $value);
    }

    private function addToChannel(DOMElement $element) {
        $this->channel->appendChild($element);
    }

    private function createChannel($title, $link, $desc, $lang, $copyright) {
        $channel = $this->createElement('channel');
        $channel->appendChild(
                $this->createElement('title', $title)
        );
        $channel->appendChild(
                $this->createElement('link', $link)
        );
        $channel->appendChild(
                $this->createElement('description', $desc)
        );
        $channel->appendChild(
                $this->createElement('language', $lang)
        );
        $channel->appendChild(
                $this->createElement('copyright', $copyright)
        );
        $channel->appendChild(
                $this->createPubDate()
        );
        return $channel;
    }

    private function createImage($url, $title, $link) {
        $image = $this->createElement('image');
        $image->appendChild(
                $this->createElement('url', $url)
        );
        $image->appendChild(
                $this->createElement('title', $title)
        );
        $image->appendChild(
                $this->createElement('link', $link)
        );
        return $image;
    }

    private function createItem($id, $title, $desc, $link, $author, $date) {
        $time = strtotime($date);
        if ($time > $this->newest) {
            $this->newest = $time;
        }
        $item = $this->createElement('item');
        $item->appendChild(
                $this->createElement('title', $title)
        );
        $item->appendChild(
                $this->createElement('description', $desc)
        );
        $item->appendChild(
                $this->createElement('link', $link)
        );
        $item->appendChild(
                $this->createElement('author', $author)
        );
        $guid = $this->createElement('guid', $id);
        $guid->setAttribute('isPermaLink', 'false');
        $item->appendChild($guid);
        $item->appendChild(
                $this->createPubDate($time)
        );
        return $item;
    }

    private function createPubDate($time = null) {
        $dateFormat = 'D, d M Y H:i:s';
        if ($time === null) {
            $date = date($dateFormat);
        } else {
            $date = date($dateFormat, $time);
        }
        return $this->createElement('pubDate', $date . ' +0100');
    }

}

?>
