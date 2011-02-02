<?php

$config = array(
	'def_path1'	=> 'calc',
	'def_path2'	=> 'index',
);

class Controller
{
	function load($thing)
	{
		return $thing;
	}

	function get_view($path)
	{
		$path = PATH_VIEW . '/' . $path . '.php';
		if (!is_readable($path))
		{
			trigger_error('Failed to get view: could not read path ' . $path, E_USER_ERROR);
			return 0;
		}
		include($path);
		return 1;
	}
}

define('PATH_VIEW', __DIR__ . '/lynx/views');
define('PATH_CONTROLLER', __DIR__ . '/lynx/controllers');

$path_info = explode("/", $_SERVER['PATH_INFO']);
$path_info[1] = isset($path_info[1]) ? $path_info[1] : $config['def_path1'];
$path_info[2] = isset($path_info[2]) ? $path_info[2] : $config['def_path2'];

if (!is_readable(PATH_CONTROLLER . '/' .$path_info[1] . '.php'))
{
	trigger_error('Could not read controller file "' . $path_info[1] . '.php"', E_USER_ERROR);
}

require_once(PATH_CONTROLLER . '/' . $path_info[1] . '.php');

$controller = $path_info[1] . 'Controller';
$controller = new $controller;
$controller->$path_info[2]();

?>
