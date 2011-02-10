<?php

class CalcController extends Controller
{
	function index()
	{
		require($this->view('calc_index'));
	}

	function db_test()
	{
		$this->load('db');
		$this->db->insert(array(
			'visits' => array(
				'time'	=> time(),
			),
		));
	}
	
	function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require($this->view('calc_results'));
	}
}

?>
