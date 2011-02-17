<?php

class CalcController extends Controller
{
	function index()
	{
		$this->load('cookies');
		$this->load('db');
		$this->load('auth');
		$this->auth->check_login('callum', 'test', true);
		require($this->view('calc_index'));
	}

	function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require($this->view('calc_results'));
	}
}

?>
