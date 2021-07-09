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

class Response
{
    /**
     * Response constructor
     * 
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Return json response
     * 
     * @param mixed $data
     * @return mixed
     */
    public static function json($data)
    {
        return json_encode($data);
    }

    /**
     * Output data
     * 
     * @param mixed $data
     */
    public static function output($data)
    {
        if(!$data) {return;}

        if(!is_string($data)) {
            $data = static::json($data);
        }
        
        echo $data;
    }
    
}