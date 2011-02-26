<?php

if (!IN_LYNX)
{
        exit;
}

class Lang extends Plugin
{
	private $array = array();
	private $lang;

	/**
	 * Sets the default language according to the configuration
	 */
	function lynx_contruct()
	{
		$this->set($this->config['lang']);
	}

	/**
	 * Sets a different language, and attempts to load the
	 * language file for that language. If it fails, the language
	 * will not be loaded and there will be loads of random errors
	 *
	 * @param string $lang The language to change to
	 */
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

	/**
	 * Loads a language file according to $file, allowing different language files
	 * for different things - you may not want to load a huge array into the
	 * memory when you're only going to use a few of the values.
	 *
	 * @param string $file The file to load from the already set language
	 */
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

	/**
	 * Gets a language entry, and runs sprintf if required.
	 *
	 * I don't think it works properly right now
	 *
	 * @param string $item The language item to get
	 * @param array $values The values to sprintf against the language item
	 */
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
