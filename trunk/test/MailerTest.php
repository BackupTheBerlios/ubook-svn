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