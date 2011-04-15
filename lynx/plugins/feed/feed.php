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
	private $handler_data = array();
	private $handler_data_wall = array();
	
	public function lynx_construct()
	{
		$this->get_plugin('db');
		$this->get_plugin('auth');
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
		while ($data = $get->fetchObject())
		{
			if (isset($this->handler_data[$data->type]))
			{
				$data = call_user_func($this->handler_data[$data->type], $data);
			}
			$end[] = $data;
		}
		return $end;
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
	
	/**
	 * Adds a handler for the get method. Any data recieved will be handled
	 * by the specified function here. $type can be an array of multiple
	 * callbacks, eg array($type => $data, $type => $data).
	 *
	 * @param string $type The type of data to handle.
	 * @param callback $data The callback.
	 */
	public function add_handler($type, $data = false)
	{
		if ($data)
		{
			$type = array($type => $data);
		}
		
		foreach ($type as $function)
		{
			if (!is_callable($function))
			{
				/**
				 * If any of the functions are not callable, we're screwed
				 * anyway - don't bother only missing that function,
				 * just return false.
				 */
				trigger_error('Supplied function not callable.');
				return false;
			}
		}
		
		$this->handler_data = array_merge($this->handler_data, $type);
		return true;
	}
	
	public function get_wall($type = false, $id = false, $limit = false)
	{
		//var_dump($this->handler_data_wall);
		//exit;
		$get = array(
			'FROM'	=> $this->config['table'],
			'LIMIT'	=> $limit ?: $this->config['d_limit'],
			'ORDER'	=> 'id DESC',
		);
		
		if ($type)
		{
			$get['WHERE']['type'] = $type;
		}
		$get['WHERE']['user_id'] = $id  ?: $this->auth->id;

		$get = $this->db->select($get);
		while ($data = $get->fetchObject())
		{
			if (isset($this->handler_data_wall[$data->type]))
			{
				$data = call_user_func($this->handler_data_wall[$data->type], $data);
			}
			$end[] = $data;
		}
		
		return $end;
	}
	
	/**
	 * Adds a handler for the get_wall method. Any data recieved will be
	 * handled by the specified function here. $type can be an array of
	 * multiple callbacks, eg array($type => $data, $type => $data).
	 *
	 * @param string $type The type of data to handle.
	 * @param callback $data The callback.
	 */
	public function add_handler_wall($type, $data = false)
	{
		if ($data)
		{
			$type = array($type => $data);
		}
		
		foreach ($type as $function)
		{
			if (!is_callable($function))
			{
				/**
				 * If any of the functions are not callable, we're screwed
				 * anyway - don't bother only missing that function,
				 * just return false.
				 */
				trigger_error('Supplied function not callable.');
				return false;
			}
		}
		
		$this->handler_data_wall = array_merge($this->handler_data_wall, $type);
		return true;
	}
}
