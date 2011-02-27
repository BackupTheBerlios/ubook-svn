<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Output.php';
require_once 'text/Template.php';

$helpTpl = Template::fromFile('view/help.html');

$output = new Output();
$output->setExpires(43200);
$output->unlinkMenuEntry('Tipps');
$output->send($helpTpl->result());
?>