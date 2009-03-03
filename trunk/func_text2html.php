<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2007 Maikel Linke
 */
// TODO check wether this function is usefull
/* replaces some chars with html code */
function text2html($text) {
	return str_replace('"','&quot;',$text);
}

?>
