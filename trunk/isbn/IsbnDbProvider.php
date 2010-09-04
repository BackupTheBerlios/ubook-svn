<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'net/DownloadThread.php';

/**
 * Provides information about an ISBN database.
 * @author maikel
 */
abstract class IsbnDbProvider {

    private $thread;

    public function provideBookFor(Isbn $isbn) {
        $this->thread = new DownloadThread($this->urlFor($isbn));
    }

    /**
     * Returns a Book object.
     * @return Book if available
     */
    public function getBook() {
        if ($this->thread == null) {
            throw new Exception('No ISBN was given.');
        }
        if (!$this->thread->isFinished()) {
            $this->thread->runAndWait();
        }
        return $this->bookFor($this->thread->getResult());
    }

    protected abstract function urlFor(Isbn $isbn);

    protected abstract function bookFor($data);
}

?>