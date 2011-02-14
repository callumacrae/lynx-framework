<?php

class Auth extends Plugin
{
	function lynx_construct()
	{
		$this->db = $this->get_plugin('db');
	}

	function check_login($user, $pass)
	{
		$user = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'USER'	=> $user,
				'pass'	=> $pass,
			),
		));

		print_r($user);
	}
}
