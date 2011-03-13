<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */
 
namespace lynx\Plugins;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class Auth extends \lynx\Core\Plugin
{
	private $failed = false;
	public $id;
	public $logged;
	public $info;

	/**
	 * Checks whether the user is already logged in.
	 *
	 * First checks whether the session already exists, and validates
	 * it against the details in the database. If this is all fine,
	 * it sets the session (or logs the user out if the details are
	 * incorrect)
	 *
	 * If the session is not set, but the cookie is, we validate the
	 * cookie against the database (the same as the session stuff)
	 */
	public function lynx_construct()
	{
		$this->get_plugin('db');
		$this->get_helper('cookies');
		$this->get_plugin('hash');

		//check the session
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

			//if it isn't an object, there are no rows and the details are false
			if (is_object($result))
			{ 
				$this->set_session($result, false, false); 
			}
			else
			{ 
				$this->logout(); 
			} 
		}
		//check the cookie
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

	/**
	 * The login method validates the information given to it against the
	 * database, and attempts to log the user in if the given info is correct.
	 *
	 * @param string $user The username to validate
	 * @param string $padd The password to validate against the username
	 * @param boolean $remember Remember the login or not?
	 */
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
			if ($result->active != 1)
			{
				$this->error = 'Error: account not active';
				return false;
			}
			
			$this->get_helper('rand');
			$result->cookie = $this->rand->string();
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

	/**
	 * set_session is a private session used by the other methods in the auth
	 * module. It sets the session info and updates the database if required.
	 *
	 * @param object $result The object returned by the db class containing the user info
	 * @param boolean $remember Remember the session or not?
	 * @param boolean $init Update the database?
	 */
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

	/**
	 * Logs the user out
	 *
	 * It destroys the session, and updates the database. It also
	 * unsets the cookie and $this->info.
	 */
	public function logout()
	{
		session_destroy();
		$this->db->update(array(
			'TABLE'		=> $this->config['table'],
			'VALUES'	=> array(
				'session'	=> null,
				'cookie'	=> null,
			),
			'WHERE'		=> array(
				'id'		=> $this->id,
			),
		));
		unset($this->cookie->{$this->config['cookie_name']});
		unset($this->info);
		return true;
	}

	/**
	 * Registers a new user.
	 *
	 * Includes a series of checks which make sure that the user is
	 * a user and not a bot (by checking stuff like email address)
	 *
	 * @param string $user The username of the account to be registered
	 * @param string $email The email address of account to be registered
	 * @param string $pass The desired password
	 */
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
			$this->error = 'Username ' . $user . ' already taken';
			return false;
		}

		//check username and password lengths
		if (strlen($user) < $this->config['user_min'])
		{
			$this->error = 'Username too short';
			return false;
		}
		if (strlen($user) > $this->config['user_max'])
		{
			$this->error = 'Username too long';
			return false;
		}
		if (strlen($pass) < $this->config['pass_min'])
		{
			$this->error = 'Password too short';
			return false;
		}
		if (strlen($pass) > $this->config['pass_max'])
		{
			$this->error = 'Password too long';
			return false;
		}
		if ($this->config['pass_complex'])
		{
			/**
			 * Check the password complexity.
			 *
			 * Due to the lack of break statements, case 3 (for example)
			 * will also be checked against cases 1 and 2, guaranteeing
			 * extra complexity.
			 */
			switch ($this->config['pass_complex'])
			{
				case 3:
					//check for symbols
					if (preg_match('/^[0-9a-zA-Z]+$/', $pass))
					{
						$this->error = 'Passwords should contain special characters.';
						return false;
					}

				case 2:
					//check for mixed case
					if (strtolower($pass) == $pass|| strtoupper($pass) == $pass)
					{
						$this->error = 'Passwords should contain both uppercase and lowercase letters';
						return false;
					}

				case 1:
					//check for a mix of letters and nums
					if (!preg_match('/[0-9]/', $pass))
					{
						$this->error = 'Your password must contain a mixture of numbers and letters';
						return false;
					}
			}
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
				$this->error = 'Email address reuse not allowed';
				return false;
			}
		}

		/**
		 * Checks whether email is valid (mostly). It doesn't allow IPs
		 * or email addresses with extentions above 4 characters long,
		 * which is basically just .museum anyway.
		 *
		 * The backreference in the regex is for the MX record check below.
		 */
		if (!preg_match('/^[A-Z0-9._%+-]+@([A-Z0-9.-]+\.[A-Z]{2,4})$/i', $email, $matches))
		{
			$this->error = 'Specified email address is not a valid email address';
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
			$this->error = 'Invalid email address';
			return false;
		}

		$this->mail->set('to', $email);

		/**
		 * Generate a random string to be used as a confirmation code or
		 * set account as active (depends what you set in the config)
		 */
		$this->get_helper('rand');
		$active = $this->config['email_act'] ? $this->rand->string() : true;

		//insert them into the database
		$this->db->insert(array(
			$this->config['table']	=> array(
				'user'			=> $user,
				'pass'			=> $this->hash->pbkdf2($pass, $user),
				'email'			=> $email,
				'active'		=> $active,
			),
		));

		//send a really ugly email. I need to change this.
		$this->mail->set('body', 'Your account has been created with the following details:' . PHP_EOL . PHP_EOL . 'Username: ' . $user . PHP_EOL . 'Password: ' . $pass);
		return $this->mail->send();
	}

	/**
	 * Activates account using the specified ID.
	 *
	 * @param int $id The ID of the account to be activated
	 */
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

	/**
	 * Deactivates account using the specified ID.
	 *
	 * @param int $id The ID of the account to be deactivated
	 */
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

	/**
	 * Confirms account against confirmation code and
	 * marks the account as actived if valid
	 *
	 * @param int $id The ID of the account to be checked
	 * @param string $code The confirmation code to be checked
	 */
	public function confirm($id, $code)
	{
		if ($code === 0)
		{
			$this->error = 'Invalid confirmation code';
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
			$this->error = 'Confirmation code not valid.';
			return false;
		}

		return $this->activate($id);
	}

	/**
	 * Resets the password of a user. It is recommended that you don't call
	 * this function without confirmation - send the user an email with a
	 * link to a page which will call this method, or it'll get abused the
	 * hell out of.
	 *
	 * @param int $id The ID of the user to have the password reset
	 * @param string $pass The password to be reset to (opt)
	 */
	public function reset_pass($id, $pass = false)
	{
		$user = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'id'	=> $id,
			),
		));

		$user = $user->fetchObject();
		if (!is_object($user))
		{
			$this->error = 'User not found';
			return false;
		}

		if (!$pass)
		{
			$pass = md5(uniqid(rand(), true));
		}

		$this->db->update(array(
			'TABLE'		=> $this->config['table'],
			'VALUES'	=> array(
				'pass'		=> $this->hash->pbkdf2($pass, $user->user),
			),
			'WHERE'		=> array(
				'id'		=> $id,
			),
		));

		$this->new_pass = $pass;
		
		$this->mail = $this->get_plugin('mail');
		$this->mail->set('subject', 'Password reset');
		$this->mail->set('to', $user->email);
		$this->mail->set('body', 'Your password has been reset to ' . $pass);
		return $this->mail->send();
	}
}
