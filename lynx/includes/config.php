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

/**
 * The config class is called when a plugin is loaded, and assigned to
 * $plugin->config. Not only does it automatically load the configuration
 * for that plugin, it also flushes all configuration changes back to the
 * configuration file for that plugin.
 */
class Config implements \arrayaccess
{
    private $container = array();

    /**
     * Construct the class
     *
     * @param array $config The config array
     * @param string $module The module that the class has been assigned to
     */
    public function __construct($config, $module)
    {
        $this->container = $config;
        $this->module = $module;
    }

    /**
     * Sets $offset to $value
     *
     * @param string $offset The array offset to set
     * @param mixed $value The value to set the offset to
     */
    public function offsetSet($offset, $value)
    {
	//wtf is this shit
        if (is_null($offset))
        {
            $this->container[] = $value;
        }
        else
        {
            $this->container[$offset] = $value;
        }
        $this->flush();
    }

    /**
     * Return whether the offset exists
     *
     * @param string $offset The offset to check for
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Unset the specifies offset
     *
     * @param string $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
        $this->flush();
    }

    /**
     * Returns the specified offset
     *
     * @param string $offset The offset to return
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    
    /**
     * Flush all changes to file.
     *
     * Uses var_export and lots of ugly code!
     */
    private function flush()
    {
    	if (isset($this->container['no_flush']) && !$this->container['no_flush'])
    	{
    		$config = '<?php' . PHP_EOL . PHP_EOL . '$config = ' . var_export($this->container, true) . ';' . PHP_EOL . PHP_EOL . '?>';
    		$path = PATH_INDEX . '/lynx/plugins/' . $this->module . '/config.php';
    		if (!is_writeable($path))
    		{
    			return false;
    		}
    		return file_put_contents($path, $config);
    	}
    }
}
