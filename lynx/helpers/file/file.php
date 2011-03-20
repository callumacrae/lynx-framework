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
		
		//append / if not already there
		if (!preg_match('/\/^/', $location))
		{
			$location .= '/';
		}

		if (!is_dir($location))
		{
			trigger_error('Directory ' . $location . ' not found');
			return false;
		}
		
		//yeah, I'm being lazy... again
		if (!is_readable($location) || !$dir = opendir($location))
		{
			trigger_error('Directory ' . $location . ' could not be opened');
			return false;
		}
		
		while ($file = readdir($dir))
		{
			//ignore . and .., as it'll confuse stuff and go all infinate on me
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			
			/**
			 * We don't actually check whether its hidden, we check
			 * whether it begins with a dot, which is basically the same
			 * thing anyway.
			 *
			 * @todo Add windoze support for hidden files
			 */
			if ($hidden && preg_match('/^\./', $file))
			{
				continue;
			}
			
			/**
			 * The next code checks whether the file is a directory. If it is
			 * a directory, it checks whether recursive is enabled. It then
			 * takes one from $recursive (only if it is an int) and calls
			 * itself to get a list of the files. If the file is not a dir in the
			 * first place, it is just added to the array.
			 */
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

		/**
		 * Yeah, I know. It's just a collection of other functions.
		 */
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
		/**
		 * Anonymous function deletes whatever is sent to it - if it is sent
		 * a directory, it will use rmdir, while it will use unlink for files.
		 */
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
		
		//I'm lazy. So sue me.
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
			
			/**
			 * If $file is a directory, cycle through deleting all the files
			 * (or calling this function if it is a directory), or just delete
			 * it if it is a file.
			 */
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
							//no continue here on purpose
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
	
	/**
	 * Uploads a file that has been sent through a form, also validates the
	 * file and can change the name of the file.
	 *
	 * @param string $file The name of the input
	 * @param array $config Values to overwrite the default config (see
	 * 	config.php)
	 */
	public function upload($file, $config = false)
	{
		//if $config is specified, merge it with config.php
		$config = ($config) ? array_merge($this->config['upload'], $config) : $this->config['upload'];

		//check whether file specified actually exists
		if (!isset($_FILES[$file]))
		{
			$this->upload_error = 'File not found';
			return false;
		}
		
		//check that file uploaded successfully
		if ($_FILES[$file]['error'] != UPLOAD_ERR_OK)
		{
			$this->upload_error = 'Error code: ' . $_FILES[$file]['error'];
			return false;
		}
		
		//check whether it is the right type
		if (!preg_match('/(' . $config['types'] . ')/', $_FILES[$file]['type']))
		{
			$this->upload_error = 'Invalid type';
			return false;
		}
		
		//check whether it is the right size
		if ($_FILES[$file]['size'] > $config['max_size'])
		{
			$this->upload_error = 'File too big';
			return false;
		}
		
		/**
		 * In the following code, we check whether the image (if it is an
		 * image) meets the specification (bigger than minimum height,
		 * smaller than max etc.). This code will fail if the image is not an
		 * image, so this functions in part as validating the image, too.
		 */
		if (strstr($_FILES[$file]['type'], 'image'))
		{
			list($width, $height) = getimagesize($_FILES[$file]['tmp_name']);
			if ($width > $config['max_width'])
			{
				$this->upload_error = 'Image too wide: maximum width ' . $config['max_width'] . 'px';
				return false;
			}
			if ($width < $config['min_width'])
			{
				$this->upload_error = 'Image not wide enough: minimum width ' . $config['min_width'] . 'px';
				return false;
			}
			if ($height > $config['max_height'])
			{
				$this->upload_error = 'Image too big: maximum height ' . $config['max_height'] . 'px';
				return false;
			}
			if ($height < $config['min_height'])
			{
				$this->upload_error = 'Image too small: minimum height ' . $config['min_height'] . 'px';
				return false;
			}
		}
		
		//this should *never* fail, but it's good to check nevertheless
		if (!file_exists($_FILES[$file]['tmp_name']))
		{
			$this->upload_error = 'Temporary file not found';
			return false;
		}
		
		//if the path doesn't end in /, add one
		if (!preg_match('/\/$/', $config['path']))
		{
			$config['path'] .= '/';
		}
		
		//if the path isn't writable, we obviously can't put stuff there.
		if (!is_writable($config['path']) && is_dir($config['path']))
		{
			$this->upload_error = 'Upload directory (' . $config['path'] . ') not writable - permissions should be set to 777';
			return false;
		}
		
		/**
		 * The reason we explode the filename is so that if there is a file
		 * by the same name already, we will get file2.zip, not file.zip2.
		 * Same thing applies for random file names - we keep the
		 * extensions.
		 */
		$this->get_helper('rand');
		$name = explode('.', $_FILES[$file]['name']);
		$name[0] = ($config['rand_name']) ? $this->rand->string() : str_replace(' ', '_', $name[0]);
		
		//we're probably being slightly too careful here, but no harm done
		while (file_exists($config['path'] . implode('.', $name)) && !$config['overwrite'])
		{
			$name[0] .= $this->rand->num(1);
		}
		$name = implode('.', $name);
		
		//move the file, and set the uploaded_data array if successful
		if (move_uploaded_file($_FILES[$file]['tmp_name'], $config['path'] . $name))
		{
			$this->uploaded_data = $this->file_info($config['path'] . $name);
			return true;
		}
		return false;
	}
}