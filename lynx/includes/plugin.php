<?php

abstract class Plugin
{
	public $config = array();

	function __construct($module)
	{
		$path = PATH_INDEX . '/lynx/plugins/' . $module . '/config.php';
		if (!is_readable($path))
		{
			return false;
		}
		include($path);
		$this->config = new Config($config, $module);
		return 1;
	}
}
