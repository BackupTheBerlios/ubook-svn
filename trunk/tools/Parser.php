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
