<?php

namespace Emblaze\Http;

class Header
{
    /**
     * Header constructor
     * 
     * @return void
     */
    private function __construct() {}

    /**
     * Set Header
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set($key,$value)
    {
        header($key.':'.$value);
    }
}