<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'ExternalServer.php';

/*
 * This list is only to initialize the list in the database.
 */
$external_servers = array(
    new ExternalServer('Bielefeld', 'http://ubook.asta-bielefeld.de/')
);
?>