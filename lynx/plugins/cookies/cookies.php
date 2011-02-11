<?php

class Cookies extends Plugin
{
	private $cookies = array();

	function lynx_construct()
	{
		$this->cookies = $_COOKIE;
	}

	function __get($cookie)
	{
		if (isset($this->cookies[$cookie]))
		{
			return $this->cookies[$cookie];
		}
		return null;
	}

	function __set($cookie, $value)
	{
		return $this->set($cookie, $value, $this->config['d_expire']);
	}

	function __isset($cookie)
	{
		return isset($this->cookies[$cookie]);
	}

	function __unset($cookie)
	{
		unset($this->cookies[$cookie]);
		return setcookie($cookie, null, time() - 3600);
	}

	function set($cookie, $value, $expire)
	{
		$this->cookies[$cookie] = $value;
		setcookie($cookie, $value, $expire);
	}
}
