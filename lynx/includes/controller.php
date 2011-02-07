<?php

class Controller
{
	function load($module)
	{
		$path = PATH_INDEX . '/lynx/plugins/' . $module . '/';
		if (!is_dir($path))
		{
			trigger_error('Could not find module: directory ' . $path . ' not found', E_USER_ERROR);
			return false;
		}

		$path .= $module . '.php';
		if (!is_readable($path))
		{
			trigger_error('Could not load module: file ' . $path . ' not found', E_USER_ERROR);
			return false;
		}

		require($path);

		$this->$module = new $module($module);
		if (method_exists($this->$module, 'lynx_construct'))
		{
			$this->$module->lynx_contruct();
		}
		return 1;
	}

	function view($path)
	{
		$path = PATH_VIEW . '/' . $path . '.php';
		if (!is_readable($path))
		{
			trigger_error('Failed to get view: could not read path ' . $path, E_USER_ERROR);
			return false;
		}
		return $path;
	}
}
