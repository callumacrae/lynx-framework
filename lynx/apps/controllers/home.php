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
		$url_helper[] = $this->url->create_a('http://google.com/', 'google');
		$url_helper[] = $this->url->mailto('callum@example.com');
		$url_helper[] = $this->url->auto('Hello world! http://example.com/test/index.php callum@yahoo.com <a href="mailto:callum@lynxphp.com">example link</a>');
		$url_helper[] = $this->url->slug('Hello world - this is a win slug!', true);
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
