<?php

class CalcController extends Controller
{
	public function index()
	{
		require($this->view('calc_index'));
	}
	
	public function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require($this->view('calc_results'));
	}
}

?>
