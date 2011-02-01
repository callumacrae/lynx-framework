<?php
namespace lynx\controllers;

class CalcController
{
	public function index()
	{
		require(VIEW_PATH . "/calc_index.php");
	}
	
	public function results()
	{
		$answer = $_POST['num1'] + $_POST['num2'];
		require(VIEW_PATH . "/calc_results.php");
	}
}
?>
