<?php

class Auth extends Plugin
{
	function lynx_construct()
	{
		$this->db = $this->get_plugin('db');
	}
}
