<?php

if (!IN_LYNX)
{
        exit;
}

class Cookies extends Plugin
{
	private $cookies = array();

	function lynx_construct()
	{
		$this->cookies = $_COOKIE;
	}

	/**
	 * Returns value ofcookie. NOTE: It will return a different result
	 * to $_COOKIE['$cookie'], as it changes the value as it is updated,
	 * therefore if you set a cookie earlier in the script you will
	 * get the new value, not the old value.
	 *
	 * @param string $cookie The name of the cookie
	 */
	function __get($cookie)
	{
		if (isset($this->cookies[$cookie]))
		{
			return $this->cookies[$cookie];
		}
		return null;
	}

	/**
	 * Sets a cookie to a new value.
	 *
	 * @param string $cookie The name of the cookie to be set
	 * @param mixed $value The value to set the cookie to
	 */
	function __set($cookie, $value)
	{
		$this->cookies[$cookie] = $value;
		return setcookie($cookie, $value, time() + $this->config['d_expire']);
	}

	/**
	 * Returns whether the cookie is set
	 *
	 * @param string $cookie The name of the cookie to be checked
	 */
	function __isset($cookie)
	{
		return isset($this->cookies[$cookie]);
	}

	/**
	 * Destroys the cookie
	 *
	 * @param string $cookie The name of the cookie to be deleted
	 */
	function __unset($cookie)
	{
		unset($this->cookies[$cookie]);
		return setcookie($cookie, null, time() - 3600);
	}
}
