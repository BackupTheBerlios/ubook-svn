<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

class CircleList {

    private $elem = null;

    public function add($v) {
        $newElem = new CircleNode($v);
        if ($this->elem != null) {
            $this->elem->insert($newElem);
        }
        $this->elem = $newElem;
    }

    public function getNext() {
        if ($this->elem == null) return null;
        $this->elem = $this->elem->next();
        return $this->elem->getValue();
    }

    public function deleteActual() {
        if ($this->elem == null) return;
        $prev = $this->elem->previous();
        $this->elem->remove();
        $this->elem = $prev;
    }

}

class CircleNode {

    private $next = null;
    private $prev = null;

    private $value = null;

    public function __construct($value) {
        $this->next = $this;
        $this->prev = $this;
        $this->value = $value;
    }

    public function insert($newElem) {
        $newElem->next = $this->next;
        $newElem->prev = $this;
        $this->next->prev = $newElem;
        $this->next = $newElem;
    }

    public function  remove() {
        $this->prev->next = $this->next;
        $this->next->prev = $this->prev;
    }

    public function next() {
        return $this->next;
    }

    public function previous() {
        if ($this->prev === $this) {
            return null;
        }
        return $this->prev;
    }

    public function getValue() {
        return $this->value;
    }

}

?>