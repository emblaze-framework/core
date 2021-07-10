<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Middleware;

use Emblaze\Http\Request;

class MiddlewareStack
{
    /**
     * Anonymous function holder
     *
     * @var mixed
     */
    protected $start;

    /**
     * MiddlewareStack constructor
     * 
     * @return mixed
     */
    public function __construct()
    {
        // initial start
        // this initial start Closure will run at the end of the middleware stack.
        $this->start = function(Request $request) {
            
            return $request;
        };
    }

    /**
     * Add new middleware stack
     *
     * @param Middleware $newCustomMiddleware
     * @return void
     */
    public function add(Middleware $newCustomMiddleware)
    {
        $next = $this->start;

        // replace the start into a new middleware
        $this->start = function(Request $request) use($newCustomMiddleware, $next) {

            // __invoke() the new middleware and pass the args $request, Closure $next
            return $newCustomMiddleware($request, $next);
        };
    }

    /**
     * Handle the Middleware Stack
     *
     * @return void
     */
    public function handle(Request $request)
    {
        // pass the $request as args into callable funciton
        return call_user_func($this->start, $request);
    }

}