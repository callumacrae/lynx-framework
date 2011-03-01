<?php
/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
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
