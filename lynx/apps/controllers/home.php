<?php

if (!IN_LYNX)
{
        exit;
}

class HomeController extends \lynx\Core\Controller
{
	function index()
	{
		$this->load('cookies');
		$this->load('hash');
		$this->load('db');
		$this->load('auth');
		$this->load('mail');
		if (!$this->auth->logged)
		{
			if($this->auth->login('callum', 'test', true))
			{
				echo 'successfully logged in';
			}
			else
			{
				echo 'failed to log in';
			}
		}
		require($this->view('home_body'));
	}
}
