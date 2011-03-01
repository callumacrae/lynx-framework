<?php

if (!IN_LYNX)
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
