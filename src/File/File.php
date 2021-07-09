<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\File;

class File
{
    /**
     * File constructor
     * 
     * @return void
     */
    private function __construct() {
        return "not null";
    }

    /**
     * Root Path
     * 
     * @return string
     */
    public static function root()
    {
        return ROOT;
    }

    /**
     * Directory separator
     * 
     * @return string
     */
    public static function ds()
    {
        return DS;
    }

    /**
     * Get file full path
     * 
     * @param string $path
     * @return string $path
     */
    public static function path($path)
    {
        // routes/web.php
        $path = static::root() . static::ds() . trim($path, '/');

        $path = str_replace(['/', '\\'], static::ds(), $path);

        return $path;
    }

    /**
     * Check if the file exists
     * 
     * @var string $path
     * @return bool
     */
    public static function exist($path)
    {
        return file_exists(static::path($path));
    }

    /**
     * Require file
     * 
     * @var string $path
     * @return mixed
     */
    public static function require_file($path)
    {
        if(static::exist($path)) {
            return require_once static::path($path);
        }
    }

    /**
     * Include file
     * 
     * @var string $path
     * @return mixed
     */
    public static function include_file($path)
    {
        if(static::exist($path)) {
            return include static::path($path);
        }
    }

    /**
     * Required directory
     * 
     * @param string $path
     * @return mixed
     */
    public static function require_directory($path)
    {
        // array_diff will exclude the '.', and '..' on array.
        $files = array_diff(scandir(static::path($path)),['.','..']);

        foreach ($files as $file) {
            $file_path = $path . static::ds() . $file;
            if(static::exist($file_path)) {
                // this will require_once all files from path folder e.g. 'routes'
                static::require_file($file_path);
            }
        }

    }
}