<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

namespace lynx\Plugins;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class Session extends \lynx\Core\Plugin
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
