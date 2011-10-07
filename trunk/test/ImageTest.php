<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
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

require_once 'PHPUnit/Framework.php';

require_once 'tools/Image.php';

require_once 'mysql_conn.php';

class ImageTest extends PHPUnit_Framework_TestCase {

    function testOrphanImages() {
        $imageIDs = array();
        $imageThumbIDs = array();
        $imgDir = opendir('img/');
        while (($file = readdir($imgDir)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $this->assertEquals('.png', substr($file, -4));
            $bookId = (int) $file;
            $this->assertTrue($bookId > 0);
            if (substr($file, -10) == '_thumb.png') {
                $imageThumbIDs[] = $bookId;
            } else {
                $imageIDs[] = $bookId;
            }
        }
        foreach ($imageThumbIDs as $bookId) {
            $this->assertTrue(array_search($bookId, $imageIDs) !== false);
        }
        $bookIDs = array();
        $query = 'select id from books order by id;';
        $result = mysql_query($query);
        while ($row = mysql_fetch_row($result)) {
            $bookIDs[] = $row[0];
        }
        $diff = array_diff($imageIDs, $bookIDs);
        $this->assertEquals(0, count($diff));
        //self::deleteDiff($diff);
    }

    static function deleteDiff($diff) {
        foreach ($diff as $bookID) {
            $img = new Image($bookID);
            $img->delete();
        }
    }

    function testDelete() {
        $imageFull = Image::PATH . '0.png';
        $imageThumb = Image::PATH . '0_thumb.png';
        touch($imageFull);
        touch($imageThumb);
        $image = new Image(0);
        $image->delete();
        if (is_file($imageFull)) {
            if (is_file($imageThumb)) {
                unlink($imageThumb);
            }
            unlink($imageFull);
            $this->fail("Image was not deleted.");
        }
    }

}

?>