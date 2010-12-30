<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'doctest/DocTest.php';

/**
 * This test does not test the class DocTest explicitly, but tests all
 * documenatation with DocTest.
 */
class DocumentationTest extends PHPUnit_Framework_TestCase {

    function testDocumentation() {
        $this->executeDir('.');
    }

    function executeDir($directory) {
        $iterator = new DirectoryIterator($directory);
        while ($iterator->valid()) {
            $entry = $iterator->getFilename();
            $path = $directory . '/' . $entry;
            $iterator->next();
            if ($entry[0] == '.') {
                continue;
            }
            if (is_file($path)) {
                if (substr($entry, -4) != '.php') {
                    continue;
                }
                if (ctype_upper($entry[0])) {
                    $test = new DocTest($path);
                    if ($test->failed()) {
                        echo $test->toString();
                        $this->fail('Doc test failed.');
                    } else {
                        if ($test->numOfPassed()) {
                            echo ',';
                        } else {
                            echo ' ';
                        }
                    }
                }
            } elseif (is_dir($path)) {
                $this->executeDir($path);
            }
        }
    }

}

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    if (isset($_SERVER['argv'][1])) {
        $test = new DocTest($_SERVER['argv'][1]);
        echo $test->toString();
    }
}

?>