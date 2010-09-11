<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * It's licensed under the GNU General Public License.
 * Copyright (C) 2010 Maikel Linke
 */

/**
 * Tests classes by executing usage examples in the class documentation.
 *
 * {@link http://svn.berlios.de/svnroot/repos/ubook/branches/doctest/ Download}
 *
 * If you have a file called <samp>example.php</samp> like this below,
 * and execute it, the DocTest class will really execute
 * <samp>add(20, 22)</samp> and compare the output to <samp>42</samp>. It will
 * output, that this test passes.
 *
 * <pre>
 *   &lt;?php
 *     // This file is example.php
 *     /**
 *      * Adds two numbers.
 *      * Usage example:
 *      * &lt;code&gt;
 *      *  echo add(20, 22); /// 42
 *      * &lt;/code&gt;
 *      {@*}
 *     function add($a, $b) {
 *         return $a + $b;
 *     }
 *
 *     if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
 *         require_once 'DocTest.php';
 *         $test = new DocTest(__FILE__);
 *         echo $test->toString();
 *     }
 *   ?&gt;
 * </pre>
 *
 * You can call the test also from another file like this:
 *
 * <code>
 *  $test = new DocTest('example.php');
 *  var_dump($test->failed()); /// bool(false)
 *  echo $test->toString();
 *  // will output:
 *  /// ----------------------------------------
 *  /// DocTest of file:
 *  ///  example.php
 *  /// Passed 1
 *  /// ----------------------------------------
 * </code>
 *
 * There are some more methods to examine the result of the test. See the list
 * below.
 *
 * The used syntax is compatible to the documentation syntax of PhpDocumentor:
 * - {@link http://pear.php.net/package/PhpDocumentor}
 *
 * The praxis of doc tests is very common under python programmers. Python has
 * a build in module for doc tests. Due it's no part of the PHP language (yet)
 * there are several PHP classes in the web following the same principle. For
 * example:
 * - {@link http://pear.php.net/package/Testing_DocTest}
 * - {@link http://github.com/xdissent/php-doctest}
 *
 * @author Maikel Linke (ubook-info@lists.berlios.de)
 * @version 2010-09-11
 */
class DocTest {

    private $testCandidate;
    private $passed;
    private $code;
    private $nextCode;
    private $expected;
    private $readingCode;
    private $executing;
    private $sourceLines;
    private $i;
    private $line;
    private $ltLine;
    private $tLine;
    private $pos;
    private $result;
    private $lastCodeLineNumber;

    /**
     * Tests <var>$testCandidate</var>.
     *
     * @param string $testCandidate PHP file with documentation
     */
    public function __construct($testCandidate) {
        require_once $testCandidate;
        $this->testCandidate = $testCandidate;
        $this->execute();
    }

    /**
     * Determines, if the test failed or not.
     *
     * <code>
     *  $test = new DocTest('example.php');
     *  var_dump($test->failed()); /// bool(false)
     * </code>
     * @return bool true, if the test failed
     */
    public function failed() {
        if ($this->i === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the number of passed assertion before the test failed or
     * terminated successfully.
     * <code>
     *  $test = new DocTest('example.php');
     *  var_dump($test->numOfPassed()); /// int(1)
     *
     *  $badTest = new DocTest('example_bad.php');
     *  var_dump($badTest->numOfPassed()); /// int(0)
     * </code>
     * @return int number of passed assertions
     */
    public function numOfPassed() {
        return $this->passed;
    }

    /**
     * Returns the last line number before an assertion failed or
     * the test terminated successfully.
     * <code>
     *  $badTest = new DocTest('example_bad.php');
     *  var_dump($badTest->getLineNumber()); /// int(10)
     * </code>
     * @return int number of the last code line
     */
    public function getLineNumber() {
        return $this->lastCodeLineNumber;
    }

    /**
     * Returns the last expected output of the tested code.
     * <code>
     *  $badTest = new DocTest('example_bad.php');
     *  var_dump($badTest->getExpected()); /// string(2) "42"
     * </code>
     * @return string expected result
     */
    public function getExpected() {
        return $this->expected;
    }

    /**
     * Returns the real output of the tested code.
     * <code>
     *  $badTest = new DocTest('example_bad.php');
     *  var_dump($badTest->getResult()); /// string(3) "440"
     * </code>
     * @return string result of the tested code
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Creates a human readable string with the result of the test.
     * <code>
     *  $badTest = new DocTest('example_bad.php');
     *  echo $badTest->toString();
     *  // will output:
     *  /// ----------------------------------------
     *  /// DocTest of file:
     *  ///  example_bad.php
     *  ///
     *  /// Failure at line 10
     *  ///
     *  /// Expected "42" but was "440"
     *  /// 
     *  /// Passed 0
     *  /// ----------------------------------------
     * </code>
     * @return string pretty representation of the test result
     */
    public function toString() {
        $s = "----------------------------------------\n"
                . "DocTest of file:\n "
                . $this->testCandidate . "\n";
        if ($this->failed()) {
            $s .= "\nFailure at line " . $this->lastCodeLineNumber . "\n\n"
                    . 'Expected "' . $this->expected . '"'
                    . ' but was "' . $this->result . "\"\n\n";
        }
        $s .= 'Passed ' . $this->passed . "\n";
        $s .= "----------------------------------------\n";
        return $s;
    }

    private function execute() {
        $this->passed = 0;
        $this->code = '';
        $this->nextCode = '';
        $this->expected = '';
        $this->readingCode = false;
        $this->executing = false;
        $this->lastCodeLineNumber = 0;
        $this->sourceLines = file($this->testCandidate);
        while (list($this->i, $this->line) = each($this->sourceLines)) {
            $this->ltLine = ltrim($this->line);
            if ($this->ltLine[0] != '*') {
                continue;
            }
            $this->ltLine = ltrim(substr($this->ltLine, 1));
            $this->tLine = rtrim($this->ltLine);
            if ($this->tLine == '<code>') {
                $this->readingCode = true;
                continue;
            }
            if ($this->tLine == '</code>') {
                $this->readingCode = false;
                if (!$this->executing) {
                    continue;
                }
            }
            if ($this->readingCode) {
                if ($this->executing) {
                    $this->nextCode = $this->ltLine;
                } else {
                    $this->code .= $this->ltLine;
                    $this->lastCodeLineNumber = $this->i + 1;
                }
                $this->pos = strpos($this->ltLine, '///');
                if ($this->pos !== false) {
                    $this->executing = true;
                    if ($this->ltLine[$this->pos + 3] == ' ') {
                        $this->expected .=
                                substr($this->ltLine, $this->pos + 4);
                    } else {
                        $this->expected .=
                                substr($this->ltLine, $this->pos + 3);
                    }
                    if ($this->pos === 0) {
                        continue;
                    }
                }
            }
            if ($this->executing) {
                $this->expected = trim($this->expected);
                ob_start();
                eval($this->code);
                $this->result = trim(ob_get_contents());
                ob_end_clean();
                $this->code = $this->nextCode;
                $this->nextCode = '';
                if ($this->result != $this->expected) {
                    return;
                }
                $this->result = '';
                $this->expected = '';
                $this->executing = false;
                $this->passed++;
            }
        }
    }

}

?>
