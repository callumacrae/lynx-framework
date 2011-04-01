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

class Feed extends \lynx\Core\Plugin
{
	public function lynx_construct()
	{
		$this->db = $this->get_plugin('db');
	}

	/**
	 * Post to the feed.
	 *
	 * @param mixed $content The content of the feed entry
	 * @param string $type The type of entry. If left blank, will default to
	 * 	whatever is set in config
	 * @param int $id The ID of the user to post as. If left blank, will default
	 * 	to the ID of current user
	 *
	 * @todo Include some checks for stuff like profile info
	 */
	public function post($content, $type = false, $id = false)
	{
		$this->hooks->call('feed_post');

		$this->db->insert(array(
			$this->config['table']	=> array(
				'user_id'		=> $id ? $id : $_SESSION['uid'],
				'type'			=> $type ?: $this->config['d_type'],
				'content'		=> $content,
				'time'			=> time(),
			),
		));
	}
	
	/**
	 * Get entries from the feed. Will return an array of objects
	 *
	 * @param string $type The type of entry. Will default to whatever is
	 * 	set in config if left blank
	 * @param int $id The ID of the user to get from. Will default to the ID
	 * 	current user if left blank
	 * @param int $limit Amount of statuses to get (SQL LIMIT format)
	 */
	public function get($type = false, $id = false, $limit = false)
	{
		$get = array(
			'FROM'	=> $this->config['table'],
			'LIMIT'	=> $limit ?: $this->config['d_limit'],
			'ORDER'	=> 'id DESC',
			'WHERE'	=> array(
				'type'	=> $type ?: $this->config['d_type'],
			)
		);
		if ($id)
		{
			$get['WHERE'] = array_merge($get['WHERE'], array(
				'user_id'	=> $id,
			));
		}

		$get = $this->db->select($get);
		$get = $get->fetchAll(\PDO::FETCH_OBJ);
		return $get;
	}
	
	/**
	 * Removes an entry from the feed.
	 *
	 * @param int $id ID of the entry to remove
	 */
	public function remove($id)
	{
		return $this->db->delete(array(
			'FROM'	=> $this->config['table'],
			'WHERE'	=> array(
				'id'		=> $id,
			),
		));
	}
}
