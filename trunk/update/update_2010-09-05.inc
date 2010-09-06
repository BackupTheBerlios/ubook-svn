<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

assert($this instanceof Updater);

$this->queryOrFail('alter table servers modify next_try datetime not null');

$this->updateFinished();
?>
