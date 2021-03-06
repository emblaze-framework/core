<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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


    /**
     * This will return the authorization header
     *
     * @return mixed
     */
    public static function get_authorization_header()
    {
        foreach (getallheaders() as $name => $value) {
            if($name == 'Authorization') {
                return $value;
            }
        }

        return false;
    }


}