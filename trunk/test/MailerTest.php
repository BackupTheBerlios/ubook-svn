<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'tools/Mailer.php';


class MailerTest extends PHPUnit_Framework_TestCase {

	function testIsValidAddress() {
		$result = Mailer::isValidAddress('juser');
		$this->assertTrue($result, 'Simple name not accepted.');
		$result = Mailer::isValidAddress('juser1');
		$this->assertTrue($result, "Number not accepted.");
		$result = Mailer::isValidAddress('juser1@example.com');
		$this->assertTrue($result, "Domain not accepted.");
		$mail = "mail@example.com\nTo: spam@example.com";
		$result = Mailer::isValidAddress($mail);
		$this->assertFalse($result, 'new line accepted');
	}

}
?>