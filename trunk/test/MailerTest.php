<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'tools/Mailer.php';

class MailerTest extends PHPUnit_Framework_TestCase {

    function testMailFromUser() {
        $index = 'mail';
        $_POST[$index] = 'juser';
        $result = Mailer::mailFromUser($index);
        $this->assertNotNull($result, 'Simple name not accepted.');
        unset($_POST[$index]);
        $_GET[$index] = 'juser';
        $result = Mailer::mailFromUser($index);
        $this->assertNotNull($result, 'From $_GET array not accepted.');
        $_POST[$index] = 'juser1';
        $result = Mailer::mailFromUser($index);
        $this->assertNotNull($result, "Number not accepted.");
        $_POST[$index] = 'juser1@example.com';
        $result = Mailer::mailFromUser($index);
        $this->assertNotNull($result, "Domain not accepted.");
        $_POST[$index] = "mail@example.com\nTo: spam@example.com";
        $result = Mailer::mailFromUser($index);
        $this->assertNull($result, 'new line accepted');
    }

}

?>