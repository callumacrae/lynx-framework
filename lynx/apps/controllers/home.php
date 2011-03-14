<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class HomeController extends \lynx\Core\Controller
{
	function index()
	{
		$this->load_plugin('lang');
		$this->load_plugin('auth');
		$this->load_plugin('mail');
		$this->load_helper('url');
		$this->load_helper('bbcode');
		$bbcode = <<<EOD
[b]Example BBCode:[/b]
					  
<a href="http://localhost">Example of HTML not working</a>
[i]Italic[/i], [b]bold[/b] and [u]underlined[/u]
[s]strikethrough and [b]inline [i]BBCode[/i][/b][/s]
[url=http://lynxphp.com/]Link to lynxphp![/url]
[color=red]Some coloured text[/color]
[font=Verdana]Verdana![/font]
[size=8]Small text :/[/size]
[img]http://shop.fitech.co.uk/wp-content/plugins/wp-e-commerce/images/no-image-uploaded.gif[/img]
EOD;
		$bbcode = $this->bbcode->parse($bbcode);
		
		$this->load_helper('form');
		
		if (!$this->auth->logged)
		{
			if($this->auth->login('callum', 'test', true))
			{
				echo 'successfully logged in';
			}
			else
			{
				echo 'failed to log in: ' . $this->auth->error;
			}
		}
		require($this->view('home_body'));
	}
}
