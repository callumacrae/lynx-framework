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
	
	/**
	 * Echos or returns a mailo link. It will "obfuscate" the email address
	 * and mailto: prefix by converting them to their ASCII counterparts,
	 * so that most bots will not recognise them. You should not rely
	 * on this to stop spam, and probably shouldn't use this on publicly
	 * accessable sites - it's security through obscurity in it's least subtle
	 * form.
	 *
	 * @param string $email The email address to use
	 * @param string $text Text to display within the anchor tag. If left
	 * 	blank, it will default to the email address (obfuscated)
	 * @param array $attr Attributes. Can be a string or array
	 * @param bool $echo Echo or return?
	 */
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

		return $this->create_a('&#109;&#97;&#105;&#108;&#116;&#111;&#58;' . $email, ($text) ? $text : $email, $attr, $echo);
	}

	/**
	 * Parses the string sent and automatically converts all URLs and email
	 * addresses into anchor links.
	 *
	 * @param string $string The string to be parsed
	 * @param string $type The type: url, email, or both
	 * @param array $attr Attributes to be passed to the mailto or create_a
	 * 	methods as they are called
	 * @param bool $echo Echo or return?
	 */
	public function auto($string, $type = 'both', $attr = false, $echo = false)
	{
		if ($type == 'both' || $type == 'url')
		{
			//$regex = '/[^(<a.*)]'; //check that it isn't already a link
			$regex .= '/(http(s?):\/\/[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(\/\S*)?)/'; //the url; will only convert stuff with http:// or https://
			//$regex .= '[^(\<\/a\>)]/i'; //check that it isn't already a link
			return preg_replace_callback($regex, create_function('$matches', 'return $this->create_a($matches[0], $attr);'), $string);
		}
	}
}