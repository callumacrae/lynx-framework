<?php

class Config implements arrayaccess
{
    private $container = array();

    public function __construct($config, $module)
    {
        $this->container = $config;
        $this->module = $module;
    }

    public function offsetSet($offset, $value)
    {
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

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
        $this->flush();
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    
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

?>
