<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * It's licensed under the GNU General Public License.
 * Copyright (C) 2010 Maikel Linke
*/

/**
 * Represents a thread running concurrent to other threads.
 *
 * PHP doesn't support threads. In most cases threads are no good idea. But
 * they can be useful while working with external resources like network
 * connections.
 *
 * All your threads will run on one CPU.
 *
 * <b>How To Use</b>
 *
 * Create two threads and let them run concurrently:
 * <code>
class NetworkThread extends Thread {

    private $handle;
    private $response = '';

    public function __construct($host) {
        parent::__construct(); // This call is important!
        $this->handle = fsockopen($host, 80);
        stream_set_blocking($this->handle, 0);
        fputs($this->handle, "GET / HTTP/1.0\r\n");
    }

    public function step() {
        $this->response .= fread($this->handle, 1024);
    }

    public function isFinished() {
        return feof($this->handle);
    }

    public function getResponse() {
        return $this->response;
    }

}

$thread1 = new NetworkThread('www.example.org');
$thread2 = new NetworkThread('www.example.net');

echo $thread1->getResponse();
echo $thread2->getResponse();
// Will print both HTTP responses.
 * </code>
 *
 *
 * @author Maikel Linke (ubook-info@lists.berlios.de)
 * @version 2010-08-16
*/
abstract class Thread {

    private static $threads = array();

    /**
     * Runs all threads until they are finished.
     *
     * While execution new threads can be added and will be executed, too.
     */
    public static function runAndWait() {
        while (sizeof(self::$threads) > 0) {
            foreach (self::$threads as $i => $t) {
                $t->step();
                if ($t->isFinished()) {
                    unset(self::$threads[$i]);
                }
            }
        }
    }

    /**
     * Creates a new thread.
     *
     * <b>Important:</b> Your subclass has to call <i>parent::__construct()</i>.
     */
    protected function __construct() {
        self::$threads[] = $this;
    }

    /**
     * Makes only one step in your computation.
     */
    public abstract function step();

    /**
     * Returns True, if the execution is finished.
     */
    public abstract function isFinished();

}
?>
