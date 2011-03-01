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
	'table'		=> 'users', //name of the table
	'cookie_name'	=> 'lynx_login',
	'email_reuse'	=> false, //allow email reuse?

	'email_act'	=> true, //enable email activation?
	'check_mx'	=> true, //check for valid MX record? (may slow script)
);
