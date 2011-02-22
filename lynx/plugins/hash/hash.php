<?php

class Hash extends Plugin
{
	public function pbkdf2($input, $s = null, $c = null, $kl = null, $a = null)
	{
		$s = is_null($s) ? $this->config['s'] : $this->pbkdf2($s);
		$c = is_null($c) ? $this->config['c'] : $c;
		$kl = is_null($kl) ? $this->config['kl'] : $kl;
		$a = is_null($a) ? $this->config['alg'] : $a;

		$hl = strlen(hash($this->config['alg'], null, true)); //hash length
		$kb = ceil($kl / $hl); //key blocks
		$dk = ''; //derived key
		
		for ($block = 1; $block <= $kb; $block ++)
		{
			$ib = $b = hash_hmac($a, $s . pack('N', $block), $input, true);

			for ($i = 1; $i < $c; $i++)
			{
				$ib ^= ($b = hash_hmac($a, $b, $input, true));
			}

			$dk .= $ib;
		}

		return base64_encode(substr($dk, 0, $kl));
	}
}
