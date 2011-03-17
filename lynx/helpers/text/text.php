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
	
	/**
	 * Returns a shortened version of the string, limited to the amount of
	 * characters or words specified.
	 *
	 * @param string $string The string to shorten
	 * @param string $type 'chars' or 'words'
	 * @param int $limit Amount of characters or words to limit to
	 */
	public function limit($string, $type = false, $limit = false, $suffix = false)
	{
		if (!$type)
		{
			$type = $this->config['d_type'];
		}
		if (!$limit)
		{
			$limit = $this->config['d_limit'];
		}
		if (!$suffix)
		{
			$suffix = $this->config['d_suffix'];
		}
		
		if ($type == 'chars' || $type == 'characters')
		{
			if (strlen($string) > $limit)
			{
				$string = substr($string, 0, $limit);
				if ($suffix)
				{
					$string .= $suffix;
				}
			}
			return $string;
		}
		else if ($type == 'words')
		{
			$words = explode(' ', $string);
			$string = array();
			
			/**
			 * The reason that we're doingn it like this instead of how the
			 * rest of the internet does it (explode, array_splice, implode),
			 * is that if I have twenty spaces, that would be counted as
			 * twenty words. That wouldn't be twenty words, it would be
			 * twenty spaces. So we're doing it like this instead :)
			 */
			foreach ($words as $word)
			{
				if ($word == null)
				{
					$string[] = null;
					continue;
				}
				
				if ($i++ == $limit)
				{
					break;
				}
				
				$string[] = $word;
			}
			
			$string = implode(' ', $string);
			
			if ($suffix)
			{
				$string .= $suffix;
			}
			return $string;
		}
		
		trigger_error('Invalid type: please use either \'words\' or \'chars\'');
		return false;
	}
}