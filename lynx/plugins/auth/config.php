<?php

if (!IN_LYNX)
{
        exit;
}

$config = array(
	'table'		=> 'users', //name of the table
	'cookie_name'	=> 'lynx_login',
	'email_reuse'	=> false, //allow email reuse?

	'email_act'	=> true, //enable email activation?
	'check_mx'	=> true, //check for valid MX record? (may slow script)

	'user_min'	=> 2, //minimum characters allowed in username
	'user_max'	=> 20, //maximum characters allowed in username

	'pass_min'	=> 6, //minimum characters allowed in password
	'pass_max'	=> 65, //maximum characters allowed in password
	/**
	 * Password complexity:
	 *
	 * 0 - don't check
	 * 1 - letters and numbers
	 * 2 - mixed case letters and numbers
	 * 3 - mixed case letters and numbers and special char
	 */
	'pass_complex'	=> 0,
);
