<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2008 Maikel Linke
 */

/**
 * This handles an image of a book.
 *
 */
class Image {
	
	var $id;
	
	/**
	 * public
	 * Constructor saving book id and determining image path
	 *
	 * @param int $id
	 * @return Image
	 */
	function Image($id) {
		$this->id = (int) $id;
	}

	/**
	 * public
	 * 
	 */
	function moveUploaded() {
		if (count($_FILES) != 1) return;
		if (!isset($_FILES['image'])) return;
		$tmp_name = $_FILES['image']['tmp_name'];
		if (!is_uploaded_file($tmp_name)) return;
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
		imagepng($image, 'img/'.$this->id.'.png');
		$max_thumb_width = 320;
		$max_thumb_height = 320; 
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
			 imagepng($thumb, 'img/'.$this->id.'_thumb.png');
		}
	}
	
	function delete() {
		$imgURL = 'img/'.$this->id.'.png';
		$thumbURL = 'img/'.$this->id.'_thumb.png';
		if (is_file($imgURL)) {
			unlink($imgURL);
		}
		if (is_file($thumbURL)) {
			unlink($thumbURL);
		}
	}
	
	/**
	 * public static
	 *
	 * @param int $id
	 * @return HTML tag
	 */
	function imgTag($id) {
		$imgURL = 'img/'.$id.'.png';
		if (!is_file($imgURL)) return '';
		$thumbURL = 'img/'.$id.'_thumb.png';
		if (is_file($thumbURL)) {
			$tag = '<img src="'.$thumbURL.'" class="bookImage" />';
			$tag = '<a href="'.$imgURL.'" target="_blank">'.$tag.'</a>';
		}
		else {
			$tag = '<img src="'.$imgURL.'" class="bookImage" />';
		}
		return $tag;
	}

}
?>
