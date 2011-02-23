<?php

if (!IN_LYNX)
{
        exit;
}

class Lang extends Plugin
{
	private $array = array();
	private $lang;

	function lynx_contruct()
	{
		$this->set($this->config['lang']);
	}

	function set($lang)
	{
		$this->lang = $lang;
		$filename = PATH_INDEX . '/lynx/lang/' . $lang . '/index.php';
		if (!is_readable($filename))
		{
			trigger_error('Could not find language file');
			return false;
		}
		include($filename);
		$this->array = $$lang;
		return true;
	}

	function load($file)
	{
		$lang = $this->lang;
		$filename = PATH_INDEX . '/lynx/lang' . $lang . '/' . $file . '.php';
		if (!is_readable($filename))
		{
			trigger_error('Could not find language file');
			return false;
		}
		include($filename);
		$this->array = array_merge($this->array, $$lang);
		return true;
	}

	function get($item, $values=null)
	{
		if (!isset($this->array[$item]))
		{
			print_r($this->array);
			return $item;
		}
		if ($values === null || !is_array($values))
		{
			return $this->array[$item];
		}
		return call_user_func_array('sprintf', array_merge(array($this->array[$item]), $values));
	}
}
