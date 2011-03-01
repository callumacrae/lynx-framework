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

class Hooks
{
	private $module_classes = array();
	public $modules = array();

	/**
	 * Refreshes the hooks, makes sure all of them are in line
	 */
	private function get_hooks()
	{
		if (count($this->module_classes) !== count($this->modules))
		{
			foreach(array_keys($this->modules) as $module)
			{
				if (isset($this->module_classes[$module]))
				{
					continue;
				}

				$file = PATH_INDEX . '/lynx/plugins/' . $module . '/hooks.php';
				if (!is_readable($file))
				{
					continue;
				}
				include($file);
				$module .= '_hooks';
				if (class_exists($module))
				{
					$this->module_classes[$module] = new $module;
					continue;
				}
				$this->module_classes = null;
			}
		}
	}
	
	/**
	 * Call the specifies hook
	 *
	 * @param string $hook The name of the hook to call
	 * @param mixed $param Optional parameters to be passed to the hook
	 */
	public function call($hook, $param = false)
	{
		$this->get_hooks();
		
		foreach ($this->module_classes as $module)
		{
			if (!$module)
			{
				continue;
			}

			if (method_exists($module, $hook))
			{
				return call_user_func(array($module, $hook), $param);
			}
		}
	}
}
