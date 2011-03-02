<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'mysql_conn.php';

class MySqlTest extends PHPUnit_Framework_TestCase {

    /**
     * Demonstrates behaviour of MySQL's "=" operator and strcmp().
     */
    function testEquals() {
        $result = mysql_query('select ("a" = "a");');
        if (!$result) {
            $this->markTestSkipped('No database connection!');
        }
        $row = mysql_fetch_row($result);
        $this->assertEquals('1', $row[0]);
        $result = mysql_query('select "a" = "A";');
        $row = mysql_fetch_row($result);
        $this->assertEquals('1', $row[0]);
        $result = mysql_query('select "Username" = "uSerName";');
        $row = mysql_fetch_row($result);
        $this->assertEquals('1', $row[0]);
        $result = mysql_query('select "User" = "Name";');
        $row = mysql_fetch_row($result);
        $this->assertEquals('0', $row[0]);
        $result = mysql_query('select "a" collate latin1_bin = "A" collate latin1_bin;');
        $row = mysql_fetch_row($result);
        $this->assertEquals('0', $row[0]);
        $result = mysql_query('select strcmp("a", "A");');
        $row = mysql_fetch_row($result);
        $this->assertEquals('0', $row[0]);
        $result = mysql_query('select strcmp("a", "a");');
        $row = mysql_fetch_row($result);
        $this->assertEquals('0', $row[0]);
        $result = mysql_query('select strcmp("a", "b");');
        $row = mysql_fetch_row($result);
        $this->assertEquals('-1', $row[0]);
        $result = mysql_query('select strcmp("b", "a");');
        $row = mysql_fetch_row($result);
        $this->assertEquals('1', $row[0]);
        $result = mysql_query('select binary "a" = "a";');
        $row = mysql_fetch_row($result);
        $this->assertEquals('1', $row[0]);
        $result = mysql_query('select binary "a" = "A";');
        $row = mysql_fetch_row($result);
        $this->assertEquals('0', $row[0]);
    }

}

?>