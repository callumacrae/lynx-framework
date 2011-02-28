<?php

if (!IN_LYNX)
{
        exit;
}

class Session extends Plugin
{
	function __get($session)
	{
		return isset($_SESSION[$session]) ? $_SESSION[$session] : null;
	}

	function __set($session, $value)
	{
		return $_SESSION[$session] = $value;
	}

	function __isset($session)
	{
		return isset($_SESSION[$session]);
	}

	function __unset($session)
	{
		unset($_SESSION[$session]);
		return true;
	}
}
