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
	 * @param bool $echo Echo or return?
	 */
	public function bbcode($string, $echo = false)
	{
		$string = htmlspecialchars($string);
		$string = nl2br($string);
		$string = preg_replace(array_keys($this->bb_array), array_values($this->bb_array), $string);
		
		if ($echo)
		{
			echo $string;
			return true;
		}
		return $string;
	}
	
	/**
	 * Returns a shortened version of the string, limited to the amount of
	 * characters or words specified.
	 *
	 * @param string $string The string to shorten
	 * @param string $type 'chars' or 'words'
	 * @param int $limit Amount of characters or words to limit to
	 * @param string $suffix Suffix to append
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
	
	/**
	 * Formats this string so that all text *like this* is sent back as bold
	 * and all text _like this_ is sent back as italic
	 *
	 * @param string $string The string to parse
	 * @param string $type 'both' (default), 'bold' or 'italic'
	 * @param bool $echo Echo or return?
	 */
	public function format($string, $type = 'both', $echo = false)
	{
		if ($type !== 'italic')
		{
			$find[] = '/\*(.+)\*/s';
			$replace[] = '<strong>$1</strong>';
		}
		
		if ($type !== 'bold')
		{
			$find[] = '/_(.+)_/s';
			$replace[] = '<i>$1</i>';
		}
		
		$string = preg_replace($find, $replace, $string);
		
		if ($echo)
		{
			echo $string;
			return true;
		}
		return $string;
	}
	
	/**
	 * Censors the string given to it using an array defined in the config
	 *
	 * @param string $string The string to censor
	 * @param bool $echo Echo or return?
	 */
	public function censor($string, $echo = false)
	{
		//very simple, but meh. May add some more stuff later
		$string = str_ireplace(array_keys($this->config['badwords']),
			array_values($this->config['badwords']), $string);
		
		if ($echo)
		{
			echo $string;
			return true;
		}
		return $string;
	}
	
	/**
	 * Smileys! Turns :) into <img src etc.
	 *
	 * @param string $string The string to parse
	 * @param string $location Location of smileys (optional)
	 * @param bool $echo Echo or return?
	 */
	public function smileys($string, $location = false, $echo = false)
	{
		if (!$location)
		{
			$location = $config['smiley_location'];
		}
		
		foreach ($config['smileys'] as $text => $image)
		{
			$smileys[$text] = '<img src="' . $location . $image . '" alt="" />';
		}
		
		$string = str_replace(array_values($smileys), array_keys($smileys), $string);
		
		if ($echo)
		{
			echo $string;
			return true;
		}
		return $string;
	}
}