<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 * TODO: Depricated.
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/AbstractIsbn.php';

class AbstractIsbnTest extends PHPUnit_Framework_TestCase {

    protected $validIsbn;
    protected $validIsbnSegmented;
    protected $validIsbnNumber;
    protected $invalidIsbn;
    protected $invalidIsbnOriginal;

	protected function setUp() {
        $this->validIsbnSegmented = '978-3897215429';
        $this->validIsbnNumber = '9783897215429';
        $this->validIsbn = new IsbnImpl($this->validIsbnSegmented);
		$this->invalidIsbnOriginal = '978 3897215429';
		$this->invalidIsbn = new IsbnImpl('978 3897215429');
	}

    function testOriginal() {
    	$result = $this->validIsbn->getOriginal();
        $this->assertEquals($this->validIsbnSegmented, $result);
        $result = $this->invalidIsbn->getOriginal();
        $this->assertEquals($this->invalidIsbnOriginal, $result);
    }

   function testSegmented() {
        $result = $this->validIsbn->getSegmented();
        $this->assertEquals($this->validIsbnSegmented, $result);
        $result = $this->invalidIsbn->getSegmented();
        $this->assertNull($result);
    }

    function testNumber() {
        $result = $this->validIsbn->getNumber();
        $this->assertEquals($this->validIsbnNumber, $result);
        $result = $this->invalidIsbn->getNumber();
        $this->assertNull($result);
    }

}

class IsbnImpl extends AbstractIsbn {

	public function isValid() {}

	public function isIsbn10() {}

	public function isIsbn13() {}

}
?>