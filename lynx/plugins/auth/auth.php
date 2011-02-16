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
				'pass'	=> $this->hash($pass),
			),
		));

		return (bool) $user->fetch();
	}

	function hash($str)
	{
		//may put something more advanced in later
		return md5($str);
	}
}
