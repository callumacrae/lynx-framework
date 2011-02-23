<?php

if (!IN_LYNX)
{
        exit;
}

class Auth extends Plugin
{
	private $failed = false;
	public $id;
	public $logged;
	public $info;

	function lynx_construct()
	{
		$this->db = $this->get_plugin('db');
		$this->cookie = $this->get_plugin('cookies');
		if (isset($_SESSION['logged']))
		{ 
			$result = $this->db->select(array(
				'FROM'	=> 'member',
				'WHERE'	=> array(
					'user'		=> $_SESSION['username'],
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
				$this->_logout(); 
			} 
		}
		else if (isset($_COOKIE[$this->config['cookie_name']]))
		{
			$this->_checkRemembered($this->cookie->{$this->config['cookie_name']}); 
		} 
	}

	function check_login($user, $pass, $remember)
	{
		$user = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'user'	=> $user,
				'pass'	=> $this->hash($pass),
			),
		));

		$result = $user->fetchObject();

		if (is_object($result))
		{
			$this->set_session($result, $remember);
			return true;
		}
		else
		{
			$this->failed = true;
			$this->_logout();
			return false;
		}
	}

	function hash($str)
	{
		//may put something more advanced in later
		return md5($str);
	}

	function update_cookie($cookie, $save)
	{
		$_SESSION['cookie'] = $cookie; 
		if ($save)
		{ 
			$cookie = serialize(array($_SESSION['username'], $cookie)); 
			$cookie_name = $this->config['cookie_name'];
			$this->cookie->$cookie_name = $cookie;
		} 
	}

	function _checkRemembered($cookie)
	{ 
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
			$this->set_session($result, true);
		} 
	}

	function set_session(&$result, $remember, $init = true)
	{
		$this->info = $result;
		$this->id = $result->id;
		$_SESSION['uid'] = $this->id;
		$_SESSION['username'] = $result->user;
		$_SESSION['cookie'] = $result->cookie;
		$_SESSION['logged'] = true;
		if ($remember)
		{
			$this->update_cookie($result->cookie, true);
		}
		if ($init)
		{
			$session = session_id();
			$ip = $_SERVER['REMOTE_ADDR'];

			$this->db->update(array(
				'TABLE'		=> $this->config['table'],
				'VALUES'	=> array(
					'session'	=> $session,
					'ip'		=> $ip,
					'cookie'	=> $_SESSION['cookie'],
				),
				'WHERE'		=> array(
					'id'		=> $this->id,
				),
			));
		}
	}

	function _logout()
	{
		echo 'Debug: _logout() called';
		return session_destroy();
	}
}
