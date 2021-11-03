<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Http\Controllers;

class BaseController
{
    public $middleware = [];
    
    /**
     * You can add the middleware using this function
     */
    public function middleware(mixed $middleware) 
    {
        if(is_array($middleware)) {
            foreach ($middleware as $value) {
                if(!in_array($value,$this->middleware)) {
                    $this->middleware[] = $value;
                }
                
            }
        }

        if(is_string($middleware)) {
            if(!in_array($middleware,$this->middleware)) {
                $this->middleware[] = $middleware;
            }
        }
        
    }

    // public function allmiddleware()
    // {
    //     dump($this->middleware);
    // }
}