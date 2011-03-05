<?php

session_start();

if ($config['debug'])
{
	error_reporting(-1);
}

//define lots of random paths and constants
define('IN_LYNX', true);

define('PATH_VIEW', __DIR__ . '/lynx/apps/views');
define('PATH_CONTROLLER', __DIR__ . '/lynx/apps/controllers');
define('PATH_INDEX', __DIR__);

//require the file that requires a file that requires a fi.. wait.
require_once('lynx/includes/includes.php');

/**
 * If PATH_INFO isn't defined, use the defaults set in the configuration
 *
 * @todo It's buggy to do it like this - use header('Location: to send them to
 * 	the right place
 */
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/')
{
	$path_info = explode('/', $_SERVER['PATH_INFO']);
	$path_info[2] = isset($path_info[2]) ? $path_info[2] : $config['d_function'];
}
else
{
	$path_info[1] = $config['d_controller'];
	$path_info[2] = $config['d_function'];
}

//why WOULDN'T the controller be found? Ah well.
if (!is_readable(PATH_CONTROLLER . '/' .$path_info[1] . '.php'))
{
	trigger_error('Could not read controller file "' . $path_info[1] . '.php"', E_USER_ERROR);
}

include(PATH_CONTROLLER . '/' . $path_info[1] . '.php');

//set up the controller and call the correct function
$controller = $path_info[1] . 'Controller';
$controller = new $controller;
$controller->$path_info[2]();
