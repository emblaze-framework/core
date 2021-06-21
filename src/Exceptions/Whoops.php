<?php

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