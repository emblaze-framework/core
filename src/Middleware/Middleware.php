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