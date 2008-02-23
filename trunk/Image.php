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
	var $path = null;
	
	/**
	 * public
	 * Constructor saving book id and determining image path
	 *
	 * @param int $id
	 * @return Image
	 */
	function Image($id) {
		$this->id = (int) $id;
		$this->determinePath();
	}

	/**
	 * public
	 * 
	 */
	function moveUploaded() {
		if (count($_FILES) != 1) return;
		if (!isset($_FILES['image'])) return;
		if (!is_uploaded_file($_FILES['image']['tmp_name'])) return;
		list($filetype, $imagetype) = split('/', $_FILES['image']['type'],2);
		if ($filetype != 'image') return;
		$new_path = 'img/'.$this->id.'.'.$imagetype;
		$this->delete();
		move_uploaded_file($_FILES['image']['tmp_name'], $new_path);
		$this->path = $new_path;
	}

	/**
	 * public
	 *
	 */
	function echo_img_tag() {
		echo '<img src="'.$this->path.'" />';
	}
	
	/**
	 * private
	 *
	 */
	function determinePath() {
		$p = 'img/'.$this->id;
		if (is_file($p.'.png')) {
			$p .= '.png';
		} else
		if (is_file($p.'.jpeg')) {
			$p .= '.jpeg';
		} else
		if (is_file($p.'.gif')) {
			$p .= '.gif';
		} else {
			return;
		}
		$this->path = $p;
	}
	
	/**
	 * private
	 *
	 */
	function delete() {
		if ($this->path == null) return;
		if (!is_file($this->path)) return;
		if (unlink($this->path)) {
			$this->path	= null;
		}
	}

}
?>
