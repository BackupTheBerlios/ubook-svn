<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'text/Template.php';
require_once 'tools/WEBDIR.php';

$tmpl = Template::fromFile('view/openSearch.xml');
$tmpl->assign('siteurl', WEBDIR);

header('Content-Type: application/opensearchdescription+xml;charset=utf-8');
//header('Content-Type: text/xml;charset=utf-8');
echo $tmpl->result();
?>