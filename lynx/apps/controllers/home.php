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

class HomeController extends \lynx\Core\Controller
{
	function index()
	{
		$this->load_plugin('lang');
		$this->load_plugin('auth');
		$this->load_plugin('mail');
		$this->load_helper('url');
		echo $this->url->create_a('http://google.com/', 'google');
		if (!$this->auth->logged)
		{
			if($this->auth->login('callum', 'test', true))
			{
				echo 'successfully logged in';
			}
			else
			{
				echo 'failed to log in: ' . $this->auth->error;
			}
		}
		require($this->view('home_body'));
	}
}
