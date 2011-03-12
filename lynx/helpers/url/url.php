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
		//store it so anon function can access it
		$this->auto_attr = $attr;

		if ($type == 'both' || $type == 'url')
		{
			$regex = '/(?<anchor><a(?:\s+(?<attr>(?:\S+?=(?:(?:\'.*?\')|(?:".*?")\s*))+))?>(?<text>.*?)<\/a\s*>)|(?<!>)(?<url>(?<proto>https?:\/{2})(?<domain>[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3})(?<path>\/\S*)?)/i';
			$string = preg_replace_callback($regex, function($matches)
			{
				global $controller;

				$attributes_string = null;

				if (strlen($matches['anchor']) > 0)
				{
					$isset = is_object($controller->url);
	
					if (strlen($matches['attr']) > 0)
					{
						preg_match_all('/' . '(?:\S+?=(?:(?:\'.*?\')|(?:".*?")\s*))' . '/i', $matches['attr'], $attributes);
						foreach ($attributes[0] as $attribute)
						{
							$attributes_split = explode('=', $attribute);
							if ($attributes_split[0] == 'href' && !preg_match('/https?:\/{2}[a-z0-9\-.]+\.[a-z]/i', trim($attributes_split[1], '" ')))
							{
								return $matches['anchor'];
							}

							if ($attributes_split [0] == 'href' && !$isset)
							{
								$attributes_string .= ' ' . $attribute;
							}
							else if ($attributes_split[0] == 'href' && $isset)
							{
								$url = trim($attributes_split[1], '" ');
							}
							else if ($isset)
							{
								$attr[$attributes_split[0]] = trim($attributes_split[1], '" ');
							}
						}
					}
					if ($isset)
					{
						$url_final = $controller->url->create_a($url, $matches['text'], $controller->attr);
					}
					return '<a' . $attributes_string . '>' . $matches['text'] . '</a>';
				}
				else
				{
					$url =  $matches['proto'] . $matches['domain'] . $matches['path'];
	
					//check whether helper is called "url"
					if (is_object($controller->url))
					{
						return $controller->url->create_a($url, $matches['text'], $controller->attr);
					}
					return '<a href="' . $url . '"' . $attributes_string . '>' . $url . '</a>';
				}
			}, $string);
		}
		
		if ($type == 'both' || $type == 'email')
		{
			$regex = '/(?<anchor><a(?:\s+(?<attr>(?:\S+?=(?:(?:\'.*?\')|(?:".*?")\s*))+))?>(?<text>.*?)<\/a\s*>)|(?<!>)(?<email>[A-Z0-9._%+-]+@([A-Z0-9.-]+\.[A-Z]{2,4})?)/i';
			$string = preg_replace_callback($regex, function($matches)
			{
				if (strlen($matches['anchor']) > 0)
				{
					return $matches['anchor'];
				}
				global $controller;
				if (is_object($controller->url))
				{
					return $controller->url->mailto($matches['email'], false, $controller->url->attr);
				}
				else
				{
					/**
					 * It is highly recommended that you use the
					 * mailto method, as it obfuscates the email. If
					 * you have renamed it, either amend the above
					 * code or don't use this function.
					 */
					return '<a href="mailto:' . $matches['email'] . '">' . $matches['email'] . '</a>';
				}
			}, $string);
		}
		
		if ($echo)
		{
			echo $string;
			return true;
		}
		return $string;
	}
	
	/**
	 * Generates a "slug" from the specified string.
	 *
	 * @param string $string The string to slug-ify
	 * @param bool $common Remove common words?
	 */
	public function slug($string, $common = false)
	{
		$string = strtolower(trim($string));
		$string = preg_replace('/[^a-z0-9 ]/', null, $string);
		if ($common)
		{
			$string = explode(' ', $string);
			$remove = array(
				'a', 'adj', 'ago', 'aint', 'also', 'am', 'an', 'and', 'any', 'are',
				'arent', 'as', 'as', 'at', 'b', 'back', 'been', 'but', 'by', 'c', 'can',
				'cannot', 'cant', 'cant', 'caption', 'cause', 'causes', 'certain',
				'certainly', 'changes', 'clearly', 'cmon', 'co', 'co.', 'com',
				'come', 'cs', 'd', 'darent', 'did', 'didnt', 'do', 'does', 'doesnt',
				'done', 'dont', 'e', 'each', 'edu', 'eg', 'else', 'end', 'et', 'etc',
				'even', 'ever', 'ex', 'f', 'far', 'few', 'for', 'from', 'g', 'get',
				'gets', 'go', 'goes', 'got', 'h', 'had', 'hadnt', 'has', 'hasnt',
				'have', 'havent', 'he', 'hed', 'hell', 'hello', 'help', 'here',
				'hereafter', 'hereby', 'herein', 'heres', 'hereupon', 'hers',
				'hes', 'hi', 'him', 'himself', 'his', 'hither', 'how', 'howbeit',
				'however', 'i', 'id', 'ie', 'if', 'ill', 'im', 'in', 'inc', 'inc.', 'into',
				'is', 'isnt', 'it', 'itd', 'itll', 'its', 'its', 'itself', 'ive', 'j', 'just',
				'k', 'know', 'known', 'knows', 'l', 'latter', 'latterly', 'least',
				'less', 'lest', 'let', 'lets', 'likely', 'likewise', 'm', 'mainly',
				'may', 'maybe', 'maynt', 'me', 'mean', 'meantime',
				'meanwhile', 'merely', 'might', 'mightnt', 'more',
				'moreover', 'most', 'mostly', 'mr', 'mrs', 'much', 'must',
				'mustnt', 'my', 'myself', 'n', 'name', 'namely', 'nd', 'near',
				'nearly', 'neednt', 'needs', 'neither','neverf', 'neverless',
				'nevertheless',  'no', 'nobody', 'non', 'none', 'nonetheless',
				'noone', 'noone', 'nor', 'not', 'nothing', 'notwithstanding',
				'now', 'nowhere', 'o', 'obviously', 'of', 'off', 'often', 'oh', 'ok',
				'okay', 'old', 'on', 'once','ones', 'ones', 'onto', 'or', 'otherwise',
				'ought', 'oughtnt', 'our', 'ours', 'ourselves', 'out', 'outside',
				'over', 'overall', 'own', 'p', 'particular', 'particularly', 'past',
				'per', 'perhaps', 'placed', 'presumably', 'probably',
				'provided', 'provides', 'q', 'que', 'qv', 'r', 'rather', 'rd', 're',
				'reasonably', 'right', 's', 'said', 'saw', 'say', 'saying', 'says',
				'secondly', 'see', 'seeing', 'seem', 'seemed', 'seeming',
				'seems', 'seen', 'self', 'selves', 'sensible', 'shall', 'shant',
				'she', 'shed', 'shell', 'shes', 'should', 'shouldnt', 'since',
				'so', 'some', 'somebody', 'someday', 'somehow', 'someone',
				'something', 'sometime', 'sometimes', 'somewhat',
				'somewhere', 'soon', 'specified', 'specify', 'specifying',
				'still', 'sub', 'such', 'sup', 't', 'take', 'taken', 'taking', 'tell',
				'tends', 'th', 'than', 'thank', 'thanks', 'thanx', 'that', 'thatll',
				'thats', 'thats', 'thatve', 'the', 'their', 'theirs', 'them',
				'themselves', 'then', 'thence', 'there', 'thereafter', 'thereby',
				'thered', 'therefore', 'therein', 'therell', 'therere', 'theres',
				'theres', 'thereupon', 'thereve', 'these', 'they', 'theyd', 'theyll',
				'theyre', 'theyve', 'thing', 'things', 'think', 'this', 'thorough',
				'thoroughly', 'those', 'though',  'through', 'throughout',
				'thru', 'thus', 'till', 'to', 'too', 'took', 'toward', 'towards',
				'tried', 'tries', 'truly', 'ts', 'twice', 'two', 'u', 'un', 'under',
				'underneath', 'undoing', 'unfortunately', 'unless', 'unlike',
				'unlikely', 'until', 'unto', 'up', 'upon', 'upwards', 'us', 'use',
				'used', 'uses', 'using', 'usually', 'v', 'various', 'via', 'viz', 'vs',
				'w', 'want', 'wants', 'was', 'wasnt', 'way', 'we', 'wed',
				'welcome', 'well', 'well', 'went', 'were', 'were', 'werent',
				'weve', 'what', 'whatever', 'whatll', 'whats', 'whatve',
				'whence', 'whenever', 'where', 'whereafter', 'whereas',
				'whereby', 'wherein', 'wheres', 'whereupon', 'wherever',
				'whether', 'which', 'whichever', 'while', 'whilst', 'whither',
				'who', 'whod', 'whoever', 'whole', 'wholl', 'whom',
				'whomever', 'whos', 'whose', 'why', 'willing', 'with',
				'within', 'without', 'wonder', 'wont', 'would', 'wouldnt', 'x',
				'y', 'yes', 'yet', 'you', 'youd', 'youll', 'your', 'youre', 'yours',
				'yourself', 'yourselves', 'youve', 'z', 'zero',
			);
			$string = array_diff($string, $remove);
			unset($remove);
			$string = implode('-', $string);
		}
		else
		{
			$string = str_replace(' ', '-', $string);
		}
		$string = preg_replace('/-{2,}/', '-', $string);
		return $string;
	}
}