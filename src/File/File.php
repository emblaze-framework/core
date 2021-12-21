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
    private function __construct() {}

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
     * Required all files in directory and in (optional)subdirectories
     * 
     * @param string $path
     * @return mixed
     */
    public static function require_directory($path, $include_subdirectories = true)
    {
        // array_diff will exclude the '.', and '..' on array.
        $files = array_diff(scandir(static::path($path)),['.','..']);

        foreach ($files as $file) {

            //-> "routes/api.php" file OR "routes/emblaze" directory
            $file_path = $path . static::ds() . $file; 

            //-> "/Users/<User>/Desktop/PHP-MVC/emblaze/emblaze/routes/api.php" file OR 
            // "/Users/<User>/Desktop/PHP-MVC/emblaze/emblaze/routes/emblaze" directory
            $full_file_path = ROOT. static::ds() . $file_path; 
            
            // Checking whether a file is directory or not
            if (is_dir($full_file_path)) {
                
                // if the file is directory  and "$include_subdirectories == true" then rerun this require_directory function
                if($include_subdirectories) {
                    // re-run
                    self::require_directory($file_path, $include_subdirectories);
                }
                
               
            }  else {
                if(static::exist($file_path)) {
                    // This will require_once all files from path folder e.g. 'routes' or 'routes/emblaze'
                    static::require_file($file_path);
                }
            }
        }
    }
    
}