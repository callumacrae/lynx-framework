<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

namespace lynx\Core;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class Controller
{
	/**
	 * Create the controller
	 *
	 * Also sets up the hooks class
	 */
	public function __construct()
	{
		$this->hooks = new Hooks;
	}

	/**
	 * Loads a plugin.
	 *
	 * @param string $module The module name
	 * @param boolean $plugin Is this being called from a plugin?
	 */
	public function load($module, $plugin = false)
	{
		if ($plugin)
		{
			if (!isset($this->hooks->modules[$module]))
			{
				$this->load($module);
			}
			return $this->$module;
		}

		//checks whether the plugin is already loaded
		if ($this->hooks->modules[$module])
		{
			return true;
		}

		//check whether the plugin directory exists
		$path = PATH_INDEX . '/lynx/plugins/' . $module . '/';
		if (!is_dir($path))
		{
			trigger_error('Could not find module: directory ' . $path . ' not found', E_USER_ERROR);
			return false;
		}

		//check whether the plugin itself exists
		$path .= $module . '.php';
		if (!is_readable($path))
		{
			trigger_error('Could not load module: file ' . $path . ' not found', E_USER_ERROR);
			return false;
		}

		require($path);

		//set the module
		$module_name = '\\lynx\\plugins\\' . $module;
		$this->$module = new $module_name($module);

		$this->hooks->modules[$module] = true;

		return true;
	}

	/**
	 * Returns the path of the specified view, or errors if the
	 * view cannot be found or read.
	 *
	 * @todo export($GLOBALS), we dont want this in an include
	 *
	 * @param string $path the name of the view file (excluding the extension)
	 */
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
