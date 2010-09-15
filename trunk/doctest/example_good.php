<?php

/**
 * Adds two numbers.
 * Usage example:
 * <code>
 *  $c = add_good(20, 22);
 *  echo $c; /// 42
 * 
 *  echo add_good($c, 11);
 *  /// 53
 * 
 *  echo $c . "\n" . $c;
 * 
 *  // results in:
 *  /// 42
 *  /// 42
 * </code>
 */
function add_good($a, $b) {
    return $a + $b;
}

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    require_once 'DocTest.php';
    $test = new DocTest(__FILE__);
    echo $test->toString();
}
?>
