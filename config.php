<?php

if (!IN_LYNX)
{
	exit;
}

/**
 * Main configuration for the entire framework here
 *
 * You probably shouldn't break it.
 */
$config = array(
	'd_controller'	=> 'home', // default controller
	'd_function'	=> 'index', // default function
	'hooks_enable'	=> true,
	'debug'		=> true, //enable debug mode
);
