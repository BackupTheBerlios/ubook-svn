<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

interface BookList {

    public function size();

    public function toHtmlRows();
}

?>