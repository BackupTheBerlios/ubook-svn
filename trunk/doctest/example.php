<?php

// This file is example.php
/**
 * Adds two numbers.
 * Usage example:
 * <code>
 *  echo add(20, 22); /// 42
 * </code>
 */
function add($a, $b) {
    return $a + $b;
}

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    require_once 'DocTest.php';
    $test = new DocTest(__FILE__);
    echo $test->toString();
}
?>