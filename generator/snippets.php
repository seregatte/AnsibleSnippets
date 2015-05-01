#!/usr/bin/env php
<?php

$modules = array_filter(array_map(function($item){
	return preg_replace('%^([a-z_0-9]*).*%', "$1", $item);
}
,preg_split('%\n%',shell_exec('ansible-doc -l'))));

$snippets = array();
foreach ($modules as $module) {
	$snippets[$module] = array_filter(array_map(function($item){
		preg_match_all('%^[\ ]*([a-z_0-9]*).*#(.*)$%', $item, $options);
		return (isset($options[1][0]) && count($options[1] > 0))? array($options[1][0], trim($options[2][0])): NULL;
	}, preg_split('%\n%',shell_exec('ansible-doc ' . $module . ' -s'))));	
}

foreach($snippets as $name => $options){
	$tpl  = '<snippet>'. PHP_EOL;
	$tpl .= '<content><![CDATA['. PHP_EOL;
	$tpl .= '${1:';
	foreach ($options as $value) {
		$tpl .= '#' . $value[0] . ' = ' . $value [1] . PHP_EOL;
	}
	$tpl .= '}';
	$tpl .= '- name: ${2:Name for ' . $name . ' module.}' . PHP_EOL;
	$tpl .= '${3:  sudo: ${4:yes}}' . PHP_EOL;
	$tpl .= '  '. $name .': ';
	$c = 4;
	foreach ($options as $value) {
		$tpl .= '${'. ++$c . ':'. $value[0] . '=${' . ++$c . ': } }';
	}
	$tpl .= PHP_EOL;
	$tpl .= '${' . ++$c . ':  when: ${' . ++$c . ': variable is defined}}' . PHP_EOL;
	$tpl .= '${' . ++$c . ':  with_items: ${' . ++$c . ': array}}' . PHP_EOL;
	$tpl .= ']]></content>' . PHP_EOL;
	$tpl .= '	<tabTrigger>' . $name . '</tabTrigger>'. PHP_EOL;
	$tpl .= '	<scope>source.yaml,source.ansible</scope>'. PHP_EOL;
	$tpl .= '</snippet>'. PHP_EOL;
	file_put_contents(dirname(__DIR__) . '/Snippets/AnsibleDoc/' . $name . '.sublime-snippet' , $tpl);
}