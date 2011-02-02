<?php

class CalcController extends Controller
{
	public function index()
	{
		$this->get_view('calc_index');
	}
	
	public function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		$this->get_view('calc_results');
	}
}

?>
