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

/**
 * Nothing to see here, move on!
 */
require_once('lynx/includes/config.php');
require_once(PATH_INDEX . '/config.php');
if ($config['hooks_enable'])
{
	require_once('hooks.php');
}
require_once('controller.php');
require_once('plugin.php');
require_once('helper.php');
