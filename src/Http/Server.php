<?php

namespace Emblaze\Http;

class Server
{
    /**
     * Server constructor.
     * 
     * @return void
     */
    private function __construct() {}

    /**
     * Check that server has the key
     * 
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SERVER[$key]);
    }

    public static function set($key,$value)
    {
        $_SERVER[$key] = $value;
        return static::get($key);
    }

    /**
     * Get the value from server by the given key
     * 
     * @param string $key
     * @return string $value
     */
    public static function get($key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }

    /**
     * Get all server data
     * 
     * @return array
     */
    public static function all()
    {
        return $_SERVER;
    }

    /**
     * Get path info for path
     * 
     * @param string $path
     * @return array
     */
    public static function path_info($path)
    {
        return pathinfo($path);
    }
}