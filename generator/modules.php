#!/usr/bin/env php
<?php

$out = fopen('php://output', 'w'); //output handler

$modules = array_filter(array_map(function($item) use ($out){
	$name = preg_replace('%^([a-z_0-9]*).*%', "$1", $item);
	if(empty($name)){
		return NULL;
	}
	fputs($out, "$name" . PHP_EOL);
}
,preg_split('%\n%',shell_exec('ansible-doc -l'))));

fclose($out); 