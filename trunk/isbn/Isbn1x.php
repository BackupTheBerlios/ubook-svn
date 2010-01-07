<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

/**
 * TODO: Depricated.
 * Provides functions of all ISBNs.
 * @author maikel
 */
abstract class Isbn1x {

	private $isValid = false;
	private $length;
	private $number;
	private $original;
	private $segmented;

	public function __construct($originalString) {
		$this->original = $originalString;
        if (preg_match('/^[0-9-]+[xX]?$/', $originalString) == 0) {
        	return;
        }
		$this->number = str_replace('-', '', $originalString);
		$this->length = strlen($this->number);
		$this->checkValidity();
		if (strpos($originalString, '-') === false) {
			$this->segmented = self::numberToSegmented($this->number);
		} else {
			$this->segmented = $originalString;
		}
	}

	public function getOriginal() {
		return $this->original;
	}

	public function getSegmented() {
		return $this->segmented;
	}

	public function getNumber() {
		return $this->number;
	}

	public function isValid() {
		return $this->isValid;
	}

	private function checkValidity() {
		$this->isValid = false;
		$n = &$this->number;
		$checksum = 0;
		if ($this->length == 10) {
			for ($i = 0; $i < 9; $i++) {
				$checksum += ($i + 1) * $n[i];
			}
			if ($n[9] == 'x' || $n[9] == 'X') {
				$checksum += 100;
			} else {
				$checksum += 10 * $n[9];
			}
			$checksum %= 11;
		} else if ($this->length == 13) {
			for ($i = 1; $i < 13; $i+=2) {
				$checksum += 3 * $n[i];
			}
			$checksum *= 3;
			for ($i = 0; $i < 13; $i+=2) {
				$checksum += $n[i];
			}
			$checksum %= 10;
		}
		if ($checksum == 0) {
			$this->isValid = true;
		}
	}

	private function numberToSegmented($n) {
		$s = '';
		if (strlen($n) == 13) {
			$s .= substr($n, 0, 3);
			$s .= '-';
			$n = substr($n, 3);
		}
		if (strlen($n) != 10) {
			throw new Exception('Invalid ISBN number length.');
		}
		$groupNum;
		if ($n[0] < 6) {
			$groupNum = $n[0];
		} else if ($n[0] == 6) {
			$groupNum = '600';
		} else if ($n[0] == 7) {
			$groupNum = '7';
		} else if ($n[0] == '8' || $n[1] < 4) {
			$groupNum = substr($n, 0, 2);
		} else if ($n[1] < 9) {
			$groupNum = substr($n, 0, 3);
		} else if ($n[2] < 9) {
			$groupNum = substr($n, 0, 4);
		} else {
			$groupNum = substr($n, 0, 5);
		}
		$s .= $groupNum . '-';
		// TODO: divide rest
	}

}

?>
