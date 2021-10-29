<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Url;

use Emblaze\Http\Server;
use Emblaze\Http\Request;

class Url
{
    /**
     * Url constructor
     */
    private function __construct() {}

    /**
     * Get path
     * 
     * @param string $path
     * @return string $path
     */
    public static function path($path)
    {
        
        // return Request::baseUrl().'/'.trim($path,'/');
        return Request::baseUrl().trim($path,'/');
    }

    /**
     * Previous url
     * 
     * @return string
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Redirect to page
     * 
     * @return void
     */
    public static function redirect($path)
    {
        header('Location: ' . $path);
        exit();
    }
}