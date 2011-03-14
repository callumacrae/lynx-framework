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
	
	/**
	 * Generates hidden input(s)
	 *
	 * @param array $hidden Array of inputs
	 * @param bool $echo Echo or return?
	 */
	public function hidden($hidden, $echo = false)
	{
		if (!is_array($hidden))
		{
			trigger_error('Invalid input: only accepts arrays');
			return false;
		}
		
		foreach($hidden as $name => $value)
		{
			$form[$name] = '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
		}
		
		$form = implode(PHP_EOL, $form);
		
		if ($echo)
		{
			echo $form;
			return true;
		}
		return $form;
	}
	
	/**
	 * Changes an array into an input. Can create multiple inputs in one go,
	 * simply send an array of arrays.
	 *
	 * @param array $input Array to turn into input
	 * @param bool $echo Echo or return?
	 */
	public function input($input, $echo = false)
	{
		if ($input == 'submit')
		{
			return '<input type="submit" />';
		}
		if (!is_array($input))
		{
			trigger_error('Invalid input: only accepts arrays');
			return false;
		}
		
		/**
		 * Returns a string of attributes, but does not include anything
		 * in the exclude argument
		 *
		 * @param array $input The input sent to the method
		 * @param array $exclude Any attributes to exclude
		 */
		$filter_params = function($input, $exclude = false)
		{
			if ($exclude)
			{
				$exclude = (is_array($exclude)) ? $exclude : array($exclude);
				foreach($input as $key => $value)
				{
					if (in_array($key, $exclude))
					{
						unset($input[$key]);
					}
				}
			}
			
			foreach ($input as $param => $value)
			{
				if (!is_array($value))
				{
					$input_str .= "$param=\"$value\" ";
				}
			}
			
			$input = substr($input_str, 0, -1);
			return $input;
		};
		
		if (isset($input['name']))
		{
			if (!isset($input['type']))
			{
				$input['type'] = $this->config['d_type'];
			}
			
			if ($input['type'] == 'textarea')
			{
				$final = '<textarea ' . $filter_params($input, array('type', 'default')) . '>' . ((!empty($input['default'])) ? $input['default'] : null) . '</textarea>';
			}
			else if ($input['type'] == 'select')
			{
				$final = '<select ' . $filter_params($input, array('type', 'options')) . '>';
				foreach ($input['options'] as $option => $value)
				{
					$params = is_array($value) ? ' ' . $filter_params($value, 'text') : null;
					$text = is_array($value) ? $value['text'] : $value;
					$final .= '<option value="' . $option . '"' . $params . '>' . $text . '</option>';
				}
				$final .= '</select>';
			}
			else
			{
				$final = '<input ' . $filter_params($input) . ' />';
			}
		}
		else
		{
			foreach($input as $single)
			{
				$final .= $this->input($single);
			}
		}
		
		if ($echo)
		{
			echo $final;
			return true;
		}
		return $final;
	}
}