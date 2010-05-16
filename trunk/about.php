<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Output.php';
require_once 'tools/Template.php';

$tmpl = Template::fromFile('view/about.html');
$output = new Output($tmpl->result());
$output->setExpires(43200);
$output->send();
?>