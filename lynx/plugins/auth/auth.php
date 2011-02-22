<?php

class Auth extends Plugin
{
	private $failed = false;
	public $id;
	public $logged;
	public $info;

	public function lynx_construct()
	{
		$this->db = $this->get_plugin('db');
		$this->cookie = $this->get_plugin('cookies');
		$this->hash = $this->get_plugin('hash');
		if (isset($_SESSION['logged']))
		{ 
			$result = $this->db->select(array(
				'FROM'	=> $this->config['table'],
				'WHERE'	=> array(
					'id'		=> $_SESSION['uid'],
					'cookie'	=> $_SESSION['cookie'],
					'session'	=> session_id(),
					'ip'		=> $_SERVER['REMOTE_ADDR'],
				),
			));
			$result = $result->fetchObject();

			if (is_object($result))
			{ 
				$this->set_session($result, false, false); 
			}
			else
			{ 
				$this->logout(); 
			} 
		}
		else if (isset($this->cookie->{$this->config['cookie_name']}))
		{
			$cookie = $this->cookie->{$this->config['cookie_name']};
			list($username, $cookie) = unserialize($cookie); 
			if (!$username or !$cookie) return; 
			$result = $this->db->select(array(
				'FROM'	=> $this->config['table'],
				'WHERE'	=> array(
					'user'	=> $username,
					'cookie'=> $cookie,
				),
			));
			if (is_object($result))
			{ 
				$result = $result->fetchObject();
				$this->set_session($result, true);
			} 
		} 
	}

	public function login($user, $pass, $remember)
	{
		$user = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'user'	=> $user,
				'pass'	=> $this->hash->pbkdf2($pass, $user),
			),
		));

		$result = $user->fetchObject();

		if (is_object($result))
		{
			$this->set_session($result, $remember, true);
			return true;
		}
		else
		{
			$this->failed = true;
			$this->logout();
			return false;
		}
	}

	private function set_session($result, $remember, $init = true)
	{
		$this->logged = true;
		$this->info = $result;
		$this->id = $result->id;
		$_SESSION['uid'] = $this->id;
		$_SESSION['username'] = $result->user;
		$_SESSION['cookie'] = $result->cookie;
		$_SESSION['logged'] = true;
		if ($remember)
		{
			$cookie = serialize(array($_SESSION['username'], $result->cookie));
			$this->cookie->{$this->config['cookie_name']} = $cookie;
		}
		if ($init)
		{
			$this->db->update(array(
				'TABLE'		=> $this->config['table'],
				'VALUES'	=> array(
					'session'	=> session_id(),
					'ip'		=> $_SERVER['REMOTE_ADDR'],
					'cookie'	=> $_SESSION['cookie'],
				),
				'WHERE'		=> array(
					'id'		=> $this->id,
				),
			));
		}
	}

	public function logout()
	{
		session_destroy();
		$this->db->update(array(
			'TABLE'		=> $this->config['table'],
			'VALUES'	=> array(
				'session'	=> null,
			),
			'WHERE'		=> array(
				'id'		=> $this->id,
			),
		));
		unset($this->cookie->{$this->config['cookie_name']});
		unset($this->info);
		return true;
	}
}
