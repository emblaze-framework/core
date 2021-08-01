<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Debug;

class Backtrace
{
    // https://www.php.net/manual/en/function.debug-backtrace.php
    
    /**
     * Backtrace constructor
     */
    public function __construct() {}

    /**
     * Will return a debug backtrace
     * 
     * @return mixed
     */
    public static function get(int $index = null)
    {
        if($index != null) {
            // will return object of debug backtrace
            return (object)debug_backtrace()[$index];
        }
        
        // will return an array of debug backtrace
        return debug_backtrace();
    }

    /**
     * Get first index
     * 
     * @return object
     */
    public static function first()
    {
        $bt = debug_backtrace();

        foreach ($bt as $index => $backtrace) {

            // This will remove/unset the array value that have a class 'Emblaze\Debug\Backtrace'
            if(array_key_exists('class',$backtrace)) {
                if($backtrace['class'] === 'Emblaze\Debug\Backtrace') {
                    unset($bt[$index]);
                    continue;
                }
            }
        }
        
        // Get the first key of array
        $firstKey = array_key_first($bt);

        return (object)$bt[$firstKey];
    }

    /**
     * Get last index
     * 
     * @return object
     */
    public static function last()
    {
        $last = count(debug_backtrace()) - 1;
        return (object)debug_backtrace()[$last];
    }

    
}