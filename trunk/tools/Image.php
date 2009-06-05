<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
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
     * Tests wether the upload-function is available.
     * @return boolean, true: uploads are available, false: uploads are not available
     */
    public static function uploadable() {
        if (!is_writable(self::PATH)) return false;
        if (!defined('GD_VERSION')) return false;
        return true;
    }

    /**
     * Returns HTML-Code containing the right img-Tag.
     *
     * @param int $id The book's id.
     * @return String HTML-Code or empty (no image present).
     */
    public static function imgTag($id) {
        $imgURL = self::PATH . $id . '.png';
        if (!is_file($imgURL)) return '';
        $thumbURL = self::PATH . $id . '_thumb.png';
        if (is_file($thumbURL)) {
            $thumbSize = getimagesize($thumbURL);
            $tag = '<img src="'.$thumbURL.'" '.$thumbSize[3].' class="bookImage" />';
            $tag = '<a href="'.$imgURL.'" target="_blank" title="Bild in Originalgröße">'.$tag.'</a>';
        }
        else {
            $tag = '<img src="'.$imgURL.'" class="bookImage" />';
        }
        return $tag;
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
        if (count($_FILES) != 1) return false;
        if (!isset($_FILES['image'])) return false;
        $tmp_name = $_FILES['image']['tmp_name'];
        if (!is_uploaded_file($tmp_name)) return false;
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
                return false;
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
            }
            else {
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
     * Removes all images belonging to a book.
     * @return void
     */
    public function delete() {
        $imgURL = self::PATH . $this->id . '.png';
        $thumbURL = self::PATH . $this->id . '_thumb.png';
        if (is_file($imgURL)) {
            unlink($imgURL);
        }
        if (is_file($thumbURL)) {
            unlink($thumbURL);
        }
    }

}
?>
