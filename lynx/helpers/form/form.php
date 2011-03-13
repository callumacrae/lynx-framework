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

class Form extends \lynx\Core\Helper
{
	/**
	 * Creates an opening form tag built from the specified params
	 *
	 * @param string $path Path for form to point to
	 * @param array $attr Attributes. Can also be string
	 * @param array $hidden Array of hidden inputs
	 * @param bool $echo Echo or return?
	 */
	public function open($path, $attr = false, $hidden = false, $echo = false)
	{
		if (!strstr($path, ':') && !preg_match('/^\//', $path))
		{
			$path = dirname($_SERVER['PHP_SELF']) . '/' . $path;
		}
		if ($attr && is_array($attr))
		{
			foreach ($attr as $key => $value)
			{
				$attr_string = " $key=\"$value\"";
			}
			$attr = $attr_string;
		}
		else if ($attr)
		{
			$attr = ' ' . $attr;
		}
		$form = '<form method="post" action="' . $path . '"' . $attr . '>';
		
		if ($hidden && is_array($hidden))
		{
			foreach($hidden as $name => $value)
			{
				$form .= PHP_EOL . '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
			}
		}
		
		if ($echo)
		{
			echo $form;
			return true;
		}
		return $form;
	}
}