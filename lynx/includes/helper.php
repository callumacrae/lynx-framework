<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */

namespace lynx\Core;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

abstract class Helper
{
    public $config = array();
    
    /**
     * Set up the Helper
     *
     * @param string $helper The name of the helper, passed by the controller load_helper method
     */
    public function __construct($helper)
    {
        $path = PATH_INDEX . '/lynx/helpers/' . $helper . '/config.php';
        if (!is_readable($path))
	{
	    return false;
	}
	include($path);
	$this->config = new Config($config, $module);
        
        if (method_exists($this, 'lynx_construct'))
	{
	    $this->lynx_construct();
	}
        
        return true;
    }
}