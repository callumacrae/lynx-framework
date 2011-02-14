<?php

class CalcController extends Controller
{
	function index()
	{
		$this->load('db');
		$this->load('auth');
		$this->auth->check_login('callum', 'test');
		require($this->view('calc_index'));
	}

	function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require($this->view('calc_results'));
	}
}

?>
