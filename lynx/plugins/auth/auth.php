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
			if ($result->active !== 1)
			{
				echo 'Error: account not active';
			}
			
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

	public function register($user, $email, $pass)
	{
		$this->mail = $this->get_plugin('mail');
		$this->mail->set('subject', 'Account registration at ...');

		//check whether username is already in use
		$select = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'user'	=> $user,
			),
		));

		if (is_object($select->fetchObject()))
		{
			echo 'Error: Username taken';
			return false;
		}

		if (!$this->config['email_reuse'])
		{
			//check whether email is already in use
			$select = $this->db->select(array(
				'FROM'	=> $this->config['table'],
				'WHERE'	=> array(
					'email'	=> $email,
				),
			));

			if (is_object($select->fetchObject()))
			{
				echo 'Error: Email address reuse not allowed';
				return false;
			}
		}

		//check whether email is valid (doesn't allow IPs)
		//the beckreference is for the MX record check below
		if (!preg_match('/^[A-Z0-9._%+-]+@([A-Z0-9.-]+\.[A-Z]{2,4})$/i', $email, $matches))
		{
			echo 'Error: Email address is not a valid email address';
			return false;
		}

		/**
		 * This part of the script checks the domain for a valid MX record.
		 * If a valid MX record is not found, the email address must be
		 * invalid, and so the script will produce an error and return false.
		 *
		 * This function may slow down your script: If there are many
		 * timeouts on registration, disable check_mx in the configuration.
		 */
		if ($this->config['check_mx'] && !checkdnsrr($matches[1], 'MX'))
		{
			echo 'Error: Invalid email address';
			return false;
		}

		$this->mail->set('to', $email);

		//generate random confirmation code or set account as active
		$active = $this->config['email_act'] ? md5(uniqid(rand(), true)) : true;

		$this->db->insert(array(
			$this->config['table']	=> array(
				'user'			=> $user,
				'pass'			=> $this->hash->pbkdf2($pass, $user),
				'email'			=> $email,
				'active'		=> $active,
			),
		));

		$this->mail->set('body', 'Your account has been created with the following details:' . PHP_EOL . PHP_EOL . 'Username: ' . $user . PHP_EOL . 'Password: ' . $pass);
		var_dump($this->mail->send());
	}

	public function activate($id)
	{
		return $this->db->update(array(
			'TABLE'		=> $this->config['table'],
			'VALUES'	=> array(
				'active'	=> 1,
			),
			'WHERE'		=> array(
				'id'		=> $id,
			),
		));
	}

	public function deactivate($id)
	{
		return $this->db->update(array(
			'TABLE'		=> $this->config['table'],
			'VALUES'	=> array(
				'active'	=> 0,
			),
			'WHERE'		=> array(
				'id'		=> $id,
			),
		));
	}

	public function confirm($id, $code)
	{
		if ($code === 0)
		{
			echo 'Error: Invalid confirmation code';
			return false;
		}

		$user = $this->db->select(array(
			'FROM'		=> $this->config['table'],
			'VALUES'	=> 'active',
			'WHERE'		=> array(
				'id'		=> $id,
				'active'	=> $code,
			),
		));

		if (!is_object($user->fetchObject()))
		{
			echo 'Error: Confirmation code not valid.';
			return false;
		}

		return $this->activate($id);
	}
}
