<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Output.php';
require_once 'tools/Statistics.php';
require_once 'tools/Template.php';

$stat = new Statistics();
$statTpl = Template::fromFile('view/statistics.html');
$stat->fillTemplate($statTpl);

$output = new Output();
$output->setExpires(43200);
$output->send($statTpl->result());
?>