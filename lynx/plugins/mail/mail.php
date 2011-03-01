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

class Mail extends \lynx\Core\Plugin
{
	private $mail = array();

	/**
	 * Sets $item to $value, eg 'to' to 'email@example.com'
	 *
	 * @param string $item The item to set
	 * @param string $value The value to set the item to
	 */
	public function set($item, $value)
	{
		$this->mail[$item] = $value;
		return 1;
	}

	/**
	 * Gets an already set item.
	 *
	 * @param string $item The item to return
	 */
	private function get($item)
	{
		if (isset($this->mail[$item]))
		{
			return $this->mail[$item];
		}
		return null;
	}

	/**
	 * Send the email.
	 *
	 * Validates all the inputs and errors if invalid.
	 */
	public function send()
	{
		if (!$this->get('to'))
		{
			trigger_error('No to address specified', E_USER_ERROR);
		}
		if (!$this->get('body'))
		{
			trigger_error('No message specified', E_USER_ERROR);
		}

		$headers = 'From: ' . $this->config['from'];

		if ($this->get('replyto'))
		{
			$headers .= "\r\n" . 'Reply-To: ' . $this->get('replyto');
		}
		if ($this->get('cc'))
		{
			$headers .= "\r\n" . 'Cc: ' . $this->get('cc');
		}
		if ($this->get('bcc'))
		{
			$headers .= "\r\n" . 'Bcc: ' . $this->get('bcc');
		}
		$headers .= "\r\nX-Mailer: PHP/" . phpversion();

		return mail($this->get('to'), $this->get('subject'), $this->get('body'), $headers);
	}
}
