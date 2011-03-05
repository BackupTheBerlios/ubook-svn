<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/Output.php';
require_once 'text/Template.php';
// TODO: target attribute not allowed in xhtml strict (search in all docs)
$tmpl = Template::fromFile('view/about.html');
$output = new Output();
$output->setExpires(43200);
$output->unlinkMenuEntry('Impressum');
$output->send($tmpl->result());
?>