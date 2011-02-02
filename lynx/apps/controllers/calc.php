<?php

class CalcController extends Controller
{
	public function index()
	{
		$this->get_view('calc_index');
	}
	
	public function results()
	{
		print_r($_POST);
		$answer = 7; //$_POST['num1'] + $_POST['num2'];
		$this->get_view('calc_results');
	}
}

?>
