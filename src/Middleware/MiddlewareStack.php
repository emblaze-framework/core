<?php

namespace Emblaze\Middleware;

use Closure;
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
     * @param Middleware $middleware
     * @return void
     */
    public function add(Middleware $middleware)
    {
        $next = $this->start;

        // replace the start into a new middleware
        $this->start = function(Request $request) use($middleware, $next) {

            // __invoke() the new middleware and pass the args $request, Closure $next
            return $middleware($request, $next);
        };
    }

    /**
     * Handle the Middleware Stack
     *
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        // pass the $request as args into callable funciton
        return call_user_func($this->start, $request);
    }

}