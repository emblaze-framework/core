<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Exceptions;

class Whoops
{
    /**
     * Whoops constructor
     */
    private function __construct() {}

    /**
     * Handle th whoops errors
     * 
     * @return void
     */
    public static function handle() {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

}