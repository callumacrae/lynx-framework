<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */
 
namespace lynx\Helpers;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

/**
 * The regex class is basically just a library of regexes
 */

class Regex extends \lynx\Core\Helper
{
	/**
	 * Return specified regex
	 *
	 * @param string $regex The name of the regex
	 */
	public function __get($regex)
	{
		if (isset($this->config['array'][$regex]))
		{
			return $this->config['array'][$regex];
		}
		return null;
	}

	/**
	 * Return whether specified regex is set
	 *
	 * @param string $regex The name of the regex
	 */
	public function __isset($regex)
	{
		return isset($this->config['array'][$regex]);
	}
}