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
require_once(__DIR__ . '/config.php');
require_once(PATH_INDEX . '/config.php');
if ($config['hooks_enable'])
{
	require_once(__DIR__ . '/hooks.php');
}
require_once(__DIR__ . '/controller.php');
require_once(__DIR__ . '/plugin.php');
