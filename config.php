<?php

if (!IN_LYNX)
{
	exit;
}

$config = array(
	'd_controller'	=> 'home', // default controller
	'd_function'	=> 'index', // default function
	'hooks_enable'	=> true,
	'debug'		=> true, //enable debug mode
);
