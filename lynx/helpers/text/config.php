<?php

$config = array(
	//bbcodes to use with the bbcode method
	'codes' 	=> array(
		'[b]{ALL}[/b]'				=> '<strong>$1</strong>',
		'[i]{ALL}[/i]'				=> '<i>$1</i>',
		'[u]{ALL}[/u]'				=> '<span style="text-decoration: underline">$1</span>',
		'[s]{ALL}[/s]'				=> '<span style="text-decoration: line-through">$1</span>',
		'[size={NUM}]{ALL}[/size]'	=> '<span style="font-size: $1">$2</span>',
		'[url={URL}]{ALL}[/url]'		=> '<a href="$1">$3</a>',
		'[color={STRING}]{ALL}[/color]'	=> '<span style="color: $1">$2</span>',
		'[font={STRING}]{ALL}[/font]'	=> '<span style="font-family: $1">$2</span>',
		'[img]{URL}[/img]'			=> '<img src="$1" alt="$1" />',
	),
	
	//defaults for the limit method
	'd_type'		=> 'words',
	'd_limit'		=> 20,
	'd_suffix'		=> '...',
);