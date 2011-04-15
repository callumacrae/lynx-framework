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

class Friends extends \lynx\Core\Plugin
{
	public function lynx_construct()
	{
		$this->get_plugin('auth');
		$this->get_plugin('db');
	}

	/**
	 * Returns an array of the specified users friends
	 *
	 * @param int $id ID of the user to get friends
	 */
	public function get($id = false)
	{
		if (!$id && !$this->auth->logged)
		{
			trigger_error('User not logged in and ID not specified');
			return false;
		}
		
		$select = $this->db->select(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'user_id'		=> $id ?: $this->auth->id,
			),
		));
		
		if (!$select->rowCount())
		{
			return 0;
		}
		
		while ($friend = $select->fetchObject())
		{
			$friends[] = (int) $friend->user2_id;
		}
		
		return $friends;
	}
	
	/**
	 * Returns an array of the specified users friends, with profile information.
	 *
	 * @param int $id ID of the user to get friends.
	 */
	public function get_info($id = false)
	{
		$this->get_plugin('profile');
		if (!$id && !$this->auth->logged)
		{
			trigger_error('User not logged in and ID not specified');
			return false;
		}
		
		return $this->profile->get_array($this->get($id));
	}
	
	/**
	 * Create a friend connection. It will listen to the configuration and
	 * create the connection both ways if required.
	 *
	 * @param int $id ID of user to add
	 * @param int $id_own ID of user to add friend to
	 * @param bool $single Runs function again if required. Do not use this.
	 */
	public function add($id, $id_own = false, $single = true)
	{
		if (!$id_own)
		{
			if (!$this->auth->logged)
			{
				trigger_error('User not logged in and ID not specified');
				return false;
			}
			$id_own = $this->auth->id;
		}
		
		$this->db->insert(array(
			$this->config['table']	=> array(
				'user_id'	=> $id_own,
				'user2_id'	=> $id,
			),
		));
		
		if (!$config['single'] && $single)
		{
			$this->add($id_own, $id, false);
		}
		
		return true;
	}
	
	/**
	 * Remove a friend connection. Again, it will listen to the config and
	 * remove the connection both ways if required and specified.
	 *
	 * @param int $id ID of user to remove.
	 * @param int $id_own ID of user to remove from
	 * @param bool $both Remove both ways?
	 */
	public function remove($id, $id_own = false, $both = true)
	{
		if (!$id_own)
		{
			if (!$this->auth->logged)
			{
				trigger_error('User not logged in and ID not specified');
				return false;
			}
			$id_own = $this->auth->id;
		}
		
		$this->db->delete(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'user_id'	=> (int) $id_own,
				'user2_id'	=> (int) $id,
			),
		));
		
		if (!$config['single'] && $both)
		{
			$this->remove($id_own, $id, false);
		}
		
		return true;
	}
}