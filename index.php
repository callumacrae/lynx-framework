<?php


require_once('lynx/includes/includes.php');

define('PATH_VIEW', __DIR__ . '/lynx/apps/views');
define('PATH_CONTROLLER', __DIR__ . '/lynx/apps/controllers');
define('PATH_INDEX', __DIR__);

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
