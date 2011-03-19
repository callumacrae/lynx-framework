<?php

/**
 * @package lynx-framework
 * @version $Id$
 * @copyright (c) lynxphp
 * @license http://creativecommons.org/licenses/by-sa/3.0/ CC by-sa
 */
 
namespace lynx\Helpers;

/**
 * @ignore
 */
if (!defined('IN_LYNX'))
{
        exit;
}

class File extends \lynx\Core\Helper
{
	/**
	 * Returns array of specified directory. Can be recursive.
	 * This function will never return . or ..
	 *
	 * @param string $location The location to return
	 * @param mixed $recursive true / false / int of how deep to go
	 * @param bool $hidden return hidden files?
	 */
	function get_dir($location, $recursive = 'string', $hidden = true)
	{
		/**
		 * The reason that we use 'string' is that we cannot set it to true
		 * or false or an int by default, as they are all valid inputs. We have
		 * to set it to something that isn't a valid input, in this case either
		 * a string or an array, a string was easier.
		 */
		if ($recursive == 'string')
		{
			$recursive = $this->config['d_recurs'];
		}
		
		if (!preg_match('/\/^/', $location))
		{
			$location .= '/';
		}

		if (!is_dir($location))
		{
			trigger_error('Directory ' . $location . ' not found');
			return false;
		}
		
		if (!is_readable($location) || !$dir = opendir($location))
		{
			trigger_error('Directory ' . $location . ' could not be opened');
			return false;
		}
		
		while ($file = readdir($dir))
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			
			if ($hidden && preg_match('/^\./', $file))
			{
				continue;
			}
			
			$full_file = $location . $file;
			if (is_dir($full_file) && ($recursive === true || $recursive > 0))
			{
				$recursive_tmp = ($recursive === true) ? true : $recursive - 1;
				$array[$file] = $this->get_dir($full_file, $recursive_tmp, $hidden);
			}
			else
			{
				$array[] = $file;
			}
		}
		return $array;
	}
	
	/**
	 * Returns an array of info about the specified file
	 *
	 * @param string $file The location of the file
	 */
	function file_info($file)
	{
		if (!file_exists($file))
		{
			trigger_error('File ' . $file . ' not found');
			return false;
		}

		return array(
			'basename'	=> basename($file),
			'dirname'		=> dirname($path),
			'group_id'	=> filegroup($file),
			'group_info'	=> posix_getgrgid(filegroup($file)),
			'last_access'	=> fileatime($file),
			'last_mod'	=> filemtime($file),
			'owner_id'	=> fileowner($file),
			'owner_info'	=> posix_getpwuid(fileowner($file)),
			'path'		=> realpath($file),
			'perms'		=> substr(sprintf('%o', fileperms($file)), -4),
			'size'		=> filesize($file),
			'type'		=> filetype($file),
		);
	}
	
	/**
	 * Deletes specified file(s)
	 *
	 * @param string $files File(s) to delete  - can be an array of files
	 * @param string $location Location of files
	 */
	function delete_files($files, $location = false)
	{
		$delete = function($file)
		{
			if (is_file($file))
			{
				unlink($file);
				return true;
			}
			if (is_dir($file))
			{
				rmdir($file);
				return true;
			}
			trigger_error('File or directory ' . $file . ' not found');
			return false;
		};
		
		if (!is_array($files))
		{
			$files = array($files);
		}

		foreach ($files as $file)
		{
			if ($location)
			{
				$file = $location . $file;
			}
			if (is_dir($file))
			{
				$dir = $this->get_dir($file, false, true);
				if (is_array($dir))
				{
					foreach($dir as $dir_file)
					{
						if ($dir_file == '.' || $dir_file == '..')
						{
							continue;
						}
						
						if (is_dir($file . '/' . $dir_file))
						{
							$this->delete_files($file . '/' . $dir_file);
						}
						
						$delete($file . '/' . $dir_file);
					}
				}
			}
			else
			{
				$delete($file);
			}
		}
		
	}
}