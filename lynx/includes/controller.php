<?php

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
