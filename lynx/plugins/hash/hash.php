<?php

class Hash extends Plugin
{
	/**
	 * Hashes $input by the hash of $s. If $s isn't specifies, use the salt
	 * specified in the config. The only non optional paramater is $input,
	 * although it is recommended to provide a salt, too.
	 *
	 * If you specify a salt, it will be hashed using the salt from the config.
	 *
	 * @param string $input The string to hash
	 * @param string $s The salt to use (will be hashed, too)
	 * @param int $s The Iteration count (should be at least 1000)
	 * @param int $kl The derived key length
	 * @param string $a The algorithm to use
	 */
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

		//base64_encoded because we don't want loads of random special chars ;)
		return base64_encode(substr($dk, 0, $kl));
	}
}
