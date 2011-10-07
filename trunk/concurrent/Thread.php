<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
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

/**
 * Represents a thread running concurrent to other threads.
 *
 * PHP doesn't support threads
 * ({@link http://php.net/manual/en/function.pcntl-fork.php exception}). In most
 * cases threads are no good idea. But they can be useful while working with
 * external resources like network connections.
 *
 * All your threads will run on one CPU. These are virtual threads.
 *
 * You can directly download the source code at BerliOS:
 * - {@link http://svn.berlios.de/svnroot/repos/ubook/branches/concurrent/}
 *
 * <b>How To Use</b>
 *
 * Create two threads and let them run concurrently:
 * <code>
 *  class NetworkThread extends Thread {
 *
 *      private $handle;
 *      private $response = '';
 *
 *      public function __construct($host) {
 *          parent::__construct(); // This call is important!
 *          $this->handle = fsockopen($host, 80);
 *          stream_set_blocking($this->handle, 0);
 *          fputs($this->handle, "GET / HTTP/1.0\r\nConnection: close\r\n\r\n");
 *      }
 *
 *      public function step() {
 *          $this->response .= fread($this->handle, 1024);
 *      }
 *
 *      public function isFinished() {
 *          return feof($this->handle);
 *      }
 *
 *      public function getResponse() {
 *          return $this->response;
 *      }
 *
 *  }
 *
 *  $thread1 = new NetworkThread('ubook.berlios.de');
 *  $thread2 = new NetworkThread('ubook.asta-bielefeld.de');
 *
 *  Thread::joinAll();
 *
 *  // The responses begin with 'HTTP'.
 *  echo substr($thread1->getResponse(), 0, 4); /// HTTP
 *  echo substr($thread2->getResponse(), 0, 4); /// HTTP
 * </code>
 *
 *
 * @author Maikel Linke
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. (ubook-info@lists.berlios.de)
 * @version 2010-09-05
 */
abstract class Thread {

    private static $threads = array();

    /**
     * Runs all threads until they are finished.
     *
     * While execution new threads can be created and will be executed, too.
     */
    public static function joinAll() {
        while (sizeof(self::$threads) > 0) {
            self::stepAll();
        }
    }

    /**
     * Iterates through all threads and calls <code>step()</code> once.
     */
    private static function stepAll() {
        foreach (self::$threads as $i => $t) {
            $t->step();
            if ($t->isFinished()) {
                unset(self::$threads[$i]);
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
     * Blocks until this thread is finished. Other threads are executed, too.
     * But it is unknown, if they are finished or not.
     */
    public function join() {
        while (!$this->isFinished()) {
            self::stepAll();
        }
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
