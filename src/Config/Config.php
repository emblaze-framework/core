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
     * Get all config file from config folder.
     *
     * @return array
     */
    public static function getall()
    {
        // Read/Scan all files from config folder.
        // loop through all files and require it to config array
        // them get it from App.php Bootstrap

        // scan the files from config folder,
        // this should return an array of file names
        $files = scandir(ROOT.'/config');

        $config = [];
        
        foreach ($files as $file) {
            if($file === "." || $file === "..") {
                // continue the foreach loop
                continue;
            }

            // removed '.php'
            $key = str_replace('.php', '', $file);
            
            $config[$key] = File::require_file('config/'.$file);
        }

        return $config;
    }

    /**
     * Return a config value
     * 
     * @param string $keys
     * @return mixed
     * 
     * @usage e.g. Config::get('app.name') or Config::get('databse.connections.mysql')
     */
    public static function get($keys = 'app')
    {
        $keys = explode('.',$keys);
        
        if(count($keys) <= 1) {
            
            $config = app()->config[$keys];

            return $config;
        }

       
        // & means is a reference
        
        $config = &app()->config;
        
        foreach ($keys as $key) {
            
            // $config is now equal to the value of $config[$key];
            // 
            $config = &$config[$key];
        }
        
        return $config;
        
    }
}