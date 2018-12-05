#!/usr/bin/env php
<?php

$modules = array_filter(array_map(function($item){
	echo "New module found..." . PHP_EOL;
	return preg_replace('%^([a-z_0-9]*).*%', "$1", $item);
}
,preg_split('%\n%',shell_exec('ansible-doc -l'))));

$snippets = array();
foreach ($modules as $module) {
	$snippets[$module] = array_filter(array_map(function($item){
		echo sprintf( "Module %s encontrado...%s", $module, PHP_EOL );
		preg_match_all('%^[\ ]*([a-z_0-9]*).*#(.*)$%', $item, $options);
		return (isset($options[1][0]) && count($options[1] > 0))? array($options[1][0], trim($options[2][0])): NULL;
	}, preg_split('%\n%',shell_exec('ansible-doc ' . $module . ' -s'))));	
}

foreach($snippets as $name => $options){
	$c = 0;
	echo sprintf("Generating snippets for a %s module...%s", $name, PHP_EOL);
	$tpl  = sprintf('<snippet>%s', PHP_EOL);
	$tpl .= sprintf('<content><![CDATA[%s', PHP_EOL);
	$tpl .= sprintf('${%d:', ++$c);
	foreach ($options as $value) {
		$tpl .= sprintf('# %s: %s %s', $value[0], $value[1], PHP_EOL);
	}
	$tpl .= sprintf('}');
	$tpl .= sprintf('- name: ${%d:Name for %s module.}%s', ++$c, $name, PHP_EOL);
	$tpl .= sprintf("  %s:%s", $name, PHP_EOL);
	foreach ($options as $value) {
		$tpl .= sprintf('${%d:   ${%d:%s}: ${%d:"#%s"}}%s', ++$c, ++$c, $value[0], ++$c, '', PHP_EOL);
	}
	$tpl .= sprintf('${%d:${%d:   %s: ${%d:%s}} %s', ++$c, ++$c, 'become', ++$c, 'true', PHP_EOL);
	$tpl .= sprintf('${%d:   %s: ${%d:%s}} %s', ++$c, 'become_method', ++$c, 'su', PHP_EOL);
	$tpl .= sprintf('${%d:   %s: ${%d:%s}} %s', ++$c, 'become_user', ++$c, 'nobody', PHP_EOL);
	$tpl .= sprintf('${%d:   %s: ${%d:%s}}} %s', ++$c, 'become_flags', ++$c, '"-s /bin/sh"', PHP_EOL);
	$tpl .= sprintf('${%d:   %s: ${%d:%s}} %s', ++$c, 'when', ++$c, 'variable is defined', PHP_EOL);
	$tpl .= sprintf('${%d:   %s: ${%d:%s}} %s', ++$c, 'with_items', ++$c, 'array', PHP_EOL);
	$tpl .= sprintf(']]></content>%s', PHP_EOL);
	$tpl .= sprintf('	<tabTrigger>%s</tabTrigger>%s', $name, PHP_EOL);
	$tpl .= sprintf('	<scope>source.yaml,source.ansible</scope>%s', PHP_EOL);
	$tpl .= sprintf('</snippet>%s', PHP_EOL);
	file_put_contents(dirname(__DIR__) . '/Snippets/AnsibleDoc/' . $name . '.sublime-snippet' , $tpl);
}
