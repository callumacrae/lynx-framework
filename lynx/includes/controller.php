<?php

if (!IN_LYNX)
{
        exit;
}

class Controller
{
	public function __construct()
	{
		$this->hooks = new Hooks;
	}

	public function load($module, $plugin = false)
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

		$module_name = '\\lynx\\plugins\\' . $module;
		$this->$module = new $module_name($module);

		$this->hooks->modules[$module] = true;

		if ($plugin)
		{
			$module =& $this->$module;
			return $module;
		}
		return true;
	}

	public function view($path)
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
