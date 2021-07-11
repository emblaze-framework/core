<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <emblaze@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Config;

use Emblaze\File\File;

class Config
{

    /**
     * Get Config file data
     *
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public static function get($name = 'app',$key = '')
    {
            // Get database config file
            $config = File::require_file('config/'.$name.'.php');
            // extract() will convert the associative array names to be variable names with there value.
            // e.g. 'host'=>'127.0.0.1' will be $host = '127.0.0.1'
            // extract($config);

            if($key) {
                return $config[$key];
            }
            return $config;
    }
}