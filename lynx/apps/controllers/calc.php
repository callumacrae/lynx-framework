<?php

if (!IN_LYNX)
{
        exit;
}

class CalcController extends Controller
{
	function index()
	{
		$this->load('hash');
		$this->load('cookies');
		$this->load('db');
		$this->load('auth');
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
		require($this->view('calc_index'));
	}

	function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require($this->view('calc_results'));
	}
}
