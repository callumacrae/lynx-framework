<?php

if (!IN_LYNX)
{
        exit;
}

class Controller
{
	function __construct()
	{
		$this->hooks = new Hooks;
	}

	function load($module)
	{
		if (isset($this->hooks->modules[$module]))
		{
			$module =& $this->$module;
			return $module;
		}

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

		$this->hooks->modules[$module] = true;
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

	function exists($module)
	{
		return (isset($this->modules[$module]) && $this->modules[$module]);
	}
}
