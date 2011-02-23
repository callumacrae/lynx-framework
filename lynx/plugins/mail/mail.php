<?php

if (!IN_LYNX)
{
        exit;
}

class Mail extends Plugin
{
	private $mail = array();

	public function set($item, $value)
	{
		$this->mail[$item] = $value;
		return 1;
	}

	private function get($thing)
	{
		if (isset($this->mail[$thing]))
		{
			return $this->mail[$thing];
		}
		return null;
	}

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
