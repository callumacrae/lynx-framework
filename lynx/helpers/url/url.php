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

class URL extends \lynx\Core\Helper
{
	/**
	 * Create an HTML anchor tag
	 *
	 * @param string $url The URL to use in the anchor
	 * @param string $text The text to use for the anchor
	 * @param array $attr Attributes. Can be a string or array
	 * @param bool $echo Echo or return?
	 */
	public function create_a($url, $text = false, $attr = false, $echo = false)
	{
		if (!$text)
		{
			$text = $url;
		}
		if ($attr)
		{
			if (is_array($attr))
			{
				$string = null;
				foreach ($attr as $key => $value)
				{
					$string .= ' ' . $key . '="' . $value . '"';
				}
				$attr = $string;
			}
		}

		$anchor = '<a href="' . $url . '"' . $attr . '>' . $text . '</a>';
		if ($echo)
		{
			echo $anchor;
			return true;
		}
		return $anchor;
	}
	
	public function mailto($email, $text = false, $attr = false, $echo = false)
	{
		//convert email into ascii
		$email_ascii = null;
		$length = strlen($email);
		for ($i = 0; $i < $length; $i++)
		{
			$email_ascii .= '&#' . ord($email[$i]) . ';';
		}
		$email = $email_ascii;

		if (!$text)
		{
			$text = $email;
		}
		
		return $this->create_a('&#109;&#97;&#105;&#108;&#116;&#111;&#58;' . $email, $text, $attr, $echo);
	}
}