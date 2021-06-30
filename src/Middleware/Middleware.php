<?php

namespace Emblaze\Middleware;

use Closure;
use Emblaze\Http\Request;

interface Middleware
{
    /**
     * __invoke() method will be required on each custom middlewares
     *
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function __invoke(Request $request, Closure $next);
}