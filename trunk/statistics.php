<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Output.php';
require_once 'tools/Statistics.php';
require_once 'text/Template.php';

$stat = new Statistics();
$statTpl = Template::fromFile('view/statistics.html');
if (file_exists(Statistics::STATS_FILE)) {
    $statTpl->addSubtemplate('stats');
    $stat->fillTemplate($statTpl);
} else {
    $statTpl->addSubtemplate('noStats');
}

$output = new Output();
$output->setExpires(43200);
$output->send($statTpl->result());
?>