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
 * @todo Cache them!
 */

class Text extends \lynx\Core\Helper
{
	/**
	 * Ready the bbcode method by parsing the array defined in the config
	 * to something regex understands
	 */
	public function lynx_construct()
	{
		$replace = array(
			'['			=> '\[',
			']'			=> '\]',
			'/'			=> '\/',
			'{ALL}'		=> '(.*)',
			'{URL}'		=> '(https?:\/{2}[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(\/\S*)?)',
			'{STRING}'		=> '([a-z]+)',
			'{NUM}'		=> '([0-9]+)',
		);
		
		foreach ($this->config['codes'] as $key => $code)
		{
			$this->bb_array['/' . str_replace(array_keys($replace), array_values($replace), $key) . '/i'] = $code;
		}
	}
	
	/**
	 * Parses any bbcode in the specified string
	 *
	 * @param string $string The string to parse
	 */
	public function bbcode($string)
	{
		$string = htmlspecialchars($string);
		$string = nl2br($string);
		return preg_replace(array_keys($this->bb_array), array_values($this->bb_array), $string);
	}
}