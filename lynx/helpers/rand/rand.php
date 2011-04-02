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

class Rand extends \lynx\Core\Helper
{
	/**
	 * Generates a random number of a specified length
	 *
	 * @todo Make it support longer lengths
	 *
	 * @param int $length The length of the number to generate
	 */
	public function num($length)
	{
		if ($length > 10)
		{
			trigger_error('Invalid length: maximum 10 where ' . $length . ' was specified');
			return false;
		}

		$min = pow(10, $length-1);
		$max = pow(10, $length)-1;
		
		$rand = rand($min, $max);

		return $rand;
	}

	/**
	 * Returns a random element from an array
	 *
	 * @param array $array The array to take random element from
	 */
	public function element($array)
	{
		$count = count($array);
		$num = rand(0, $count-1);
		$element = $array[$num];
		
		return $element;
	}
	
	/**
	 * Returns a random string
	 *
	 * @param int $length The length of the string to generate
	 */
	public function string($length = 32)
	{
		for ($i = 0; $i < $length/32; $i++)
		{
			$string .= md5(uniqid(rand(), true));
		}
		
		$string = substr($string, 0, $length);

		return $string;
	}
}