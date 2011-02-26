<?php

namespace lynx\Core;

if (!IN_LYNX)
{
        exit;
}

abstract class Plugin
{
	public $config = array();

	public function __construct($module)
	{
		$path = PATH_INDEX . '/lynx/plugins/' . $module . '/config.php';
		if (!is_readable($path))
		{
			return false;
		}
		include($path);
		$this->config = new Config($config, $module);

		if (method_exists($this, 'lynx_construct'))
		{
			$this->lynx_construct();
		}

		return 1;
	}

	public function get_plugin($plugin)
	{
		$controller =& $GLOBALS['controller'];
		return $controller->load($plugin);
	}
}
