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