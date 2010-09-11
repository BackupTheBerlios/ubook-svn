<?php

/**
 * Adds two numbers (wrongly).
 * Usage example:
 * <code>
 *  $c = add_bad(20, 22);
 *  echo $c;
 *  // will not result in:
 *  /// 42
 * </code>
 */
function add_bad($a, $b) {
    return $a * $b;
}

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    require_once 'DocTest.php';
    $test = new DocTest(__FILE__);
    echo $test->toString();
}
?>
