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
		$this->load('lang');
		$this->load('auth');
		$this->load('feed');
		//$this->feed->post('test status wooo', 'status', 1);
		//$get = $this->feed->get();
		//print_r($get);
		if (!$this->auth->logged)
		{
			if($this->auth->login('callum', 'test', true))
			{
				echo 'successfully logged in';
			}
			else
			{
				echo $this->auth->error;
				//echo 'failed to log in';
			}
		}
		require($this->view('home_body'));
	}
}
