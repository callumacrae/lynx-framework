<?php

class HelloController extends Controller
{
	public function index()
	{
		$this->load('lang');
		echo $this->lang->get('hello_world');
	}
}

?>
