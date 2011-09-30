#!/usr/bin/php
<?php

if ($argc < 2) {
    echo "Usage: $argv[0] MyFile.php [MySecondFile.php ...]\n";
    return 1;
}

require_once 'DocTest.php';

for ($i = 1; $i < $argc; $i++) {
    $test = new DocTest($argv[$i]);
    if ($test->failed()) {
        echo $test->toString();
        return 1;
    } else {
        if ($test->numOfPassed() < 1) {
            fwrite(STDERR, "Warning: $argv[$i] contained no tests.\n");
        }
    }
}
?>