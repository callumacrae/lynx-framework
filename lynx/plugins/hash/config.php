<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

$config = array(
	'salt'	=> '342af42af7a938c33eea5b0a5f872afd', // the salt: You should probably change this
	'alg'	=> 'sha256', //algorithm
	'kl'	=> 32, //key length 
	'c'	=> 1000, //iteration count (must be > 1000)
);

?>
