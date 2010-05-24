<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/*
 * A collection of parsing functions
*/
class Parser {

    /**
     * Convert all applicable characters to HTML entities.
     * The charset parameter was added in PHP version 4.1.0.
     * @param string $text normal text in UTF-8
     * @return string HTML encoded text
     */
    public static function text2html($text) {
        $quoteStyle = ENT_QUOTES;
        $charset = 'UTF-8';
        $html = htmlentities($text, $quoteStyle, $charset);
        return $html;
    }

}
?>
