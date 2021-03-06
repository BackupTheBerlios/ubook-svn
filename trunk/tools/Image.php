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

/**
 * This handles an image of a book.
 */
class Image {
    /**
     * @var String PATH of image folder.
     */
    const PATH = 'img/';
    /**
     * @var String FILE_INDEX used name of input field for image uploads
     */
    const FILE_INDEX = 'image';

    private static $filename = null;

    /**
     * Tests wether the upload-function is available.
     * @return boolean, true: uploads are available, false: uploads are not available
     */
    public static function uploadable() {
        if (!is_writable(self::PATH))
            return false;
        if (!function_exists('imagecreatefrompng'))
            return false;
        return true;
    }

    /**
     * Checks, wether a file was uploaded.
     * @return boolean true, if a file was uploaded
     */
    public static function wasUploaded() {
        if (self::upladedFileName())
            return true;
        else
            return false;
    }

    /**
     * Guesses, if this image can be stored in the memory.
     * @return boolean true, if the file can be stored (probably)
     */
    public static function isStorable() {
        $imageSize = getimagesize(self::upladedFileName());
        /* How many bytes needs this image in the memory? */
        $imagePixels = $imageSize[0] * $imageSize[1];
        $bytesPerPixel = $imageSize['bits'] / 8;
        $imageBytes = $imagePixels * $bytesPerPixel * $imageSize['channels'];
        /* How many bytes needs the calling script? We guess 1MB. */
        $scriptBytes = (int) 1E6;
        $memoryLimit = self::returnBytes(ini_get('memory_limit'));
        if (($scriptBytes + $imageBytes) > $memoryLimit)
            return false;
        return true;
    }

    /**
     * Returns HTML-Code containing the right img-Tag.
     *
     * @param int $id The book's id.
     * @return String HTML-Code or empty (no image present).
     */
    public static function imgTag($id) {
        $imgURL = self::imageUrl($id);
        if (!is_file($imgURL))
            return '';
        $thumbURL = self::PATH . $id . '_thumb.png';
        if (is_file($thumbURL)) {
            $thumbSize = getimagesize($thumbURL);
            $tag = '<img src="' . $thumbURL . '" ' . $thumbSize[3] . ' class="bookImage" />';
            $tag = '<a href="' . $imgURL . '" target="_blank" title="Bild in Originalgröße">' . $tag . '</a>';
        } else {
            $tag = '<img src="' . $imgURL . '" class="bookImage" />';
        }
        return $tag;
    }

    private static function upladedFileName() {
        if (self::$filename !== null) {
            return self::$filename;
        }
        if (count($_FILES) == 1 && isset($_FILES[self::FILE_INDEX])) {
            // TODO: check $_FILES[self::FILE_INDEX]['error'] == UPLOAD_ERR_OK
            $tmp_name = $_FILES[self::FILE_INDEX]['tmp_name'];
            if (is_uploaded_file($tmp_name)) {
                self::$filename = $tmp_name;
                return self::$filename;
            }
        }
        self::$filename = false;
    }

    private $id;

    /**
     * Constructor saving book id and determining image path.
     *
     * @param int $id The book-ID.
     */
    public function Image($id) {
        $this->id = (int) $id;
    }

    /**
     * Moves reads the uploaded file and writes it in the image directory.
     *
     * @return boolean True on success.
     */
    public function moveUploaded() {
        $tmp_name = self::upladedFileName();
        if ($tmp_name == null)
            return;
        if (!self::isStorable())
            return;
        $imageSize = getimagesize($tmp_name);
        switch ($imageSize[2]) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($tmp_name);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($tmp_name);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($tmp_name);
                break;
            default:
                return;
        }
        $this->delete();
        imagepng($image, self::PATH . $this->id . '.png');
        $max_thumb_width = 250;
        $max_thumb_height = 250;
        $img_width = imagesx($image);
        $img_height = imagesy($image);
        if ($img_width > $max_thumb_width || $img_height > $max_thumb_height) {
            if ($img_width > $img_height) {
                $thumb_width = $max_thumb_width;
                $thumb_height = $thumb_width * $img_height / $img_width;
            } else {
                $thumb_height = $max_thumb_height;
                $thumb_width = $thumb_height * $img_width / $img_height;
            }
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresized($thumb, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $img_width, $img_height);
            imagepng($thumb, self::PATH . $this->id . '_thumb.png');
            imagedestroy($thumb);
        }
        imagedestroy($image);
        return true;
    }

    /**
     * Checks whether an image for the given book exists.
     *
     * @param $id identifiyng the book
     * @return bool true, if an image exists
     */
    public static function exists($id) {
        return is_file(self::imageUrl($id));
    }

    /**
     * Removes all images belonging to a book.
     * @return void
     */
    public function delete() {
        $imgURL = $this->imageUrl($this->id);
        $thumbURL = $this->thumbUrl($this->id);
        if (is_file($imgURL)) {
            unlink($imgURL);
        }
        if (is_file($thumbURL)) {
            unlink($thumbURL);
        }
    }

    private static function imageUrl($id) {
        return self::PATH . $id . '.png';
    }

    private static function thumbUrl($id) {
        return self::PATH . $id . '_thumb.png';
    }

    /**
     * Calculates the byte number of a short ini_get value.
     * @copyright copied from http://php.net/manual/en/function.ini-get.php
     * @param string $val value from ini_get
     * @return int $val in bytes
     */
    private static function returnBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

}

?>
