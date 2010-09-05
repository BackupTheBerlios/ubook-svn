<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

assert($this instanceof Updater);

$this->queryOrFail('create table db_version (db_version date not null);');
$this->queryOrFail('insert into db_version values ("0");');

?>
