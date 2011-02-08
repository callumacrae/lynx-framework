<?php

class Hooks
{
	private $module_classes = array();
	public $modules = array();

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
	
	function call($hook, $param=false)
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
