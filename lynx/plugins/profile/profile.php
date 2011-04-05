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

class Profile extends \lynx\Core\Plugin
{
	function lynx_construct()
	{
		
	}
	
	/**
	 * The get method gets all info from a specified table (usually just the
	 * users table) that the auth plugin doesn't get.
	 *
	 * @param int $id ID of the user to fetch
	 * @param string $table Name of the table to use
	 */
	function get($id = false, $table = false)
	{
		$this->get_plugin('auth');
		$this->get_plugin('db');
		
		//if ID isn't specified, assume ID of current user
		if (!$id)
		{
			if (!$this->auth->logged)
			{
				trigger_error('User logged in and ID not specified');
				return false;
			}
			$id = $this->auth->id;
		}
		
		//get table name if $table isn't set
		if (!$table)
		{
			$table = $this->config['table'] ?: $this->auth->config['table'];
		}
		
		$select = $this->db->select(array(
			'FROM'	=> $table,
			'WHERE'	=> array(
				'id'		=> $id,
			),
		));
		
		if (!$select->rowCount())
		{
			trigger_error('User not found');
		}

		$select = $select->fetchObject();
		//we don't want the password passed back!
		unset($select->pass, $select->cookie, $select->session);
		
		return $select;
	}
}