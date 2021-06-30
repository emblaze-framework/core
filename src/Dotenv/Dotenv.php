<?php

namespace Emblaze\Dotenv;

use Dotenv\Dotenv as  vlucasDotenv;

class Dotenv
{
    /**
     * Dotenv constructor
     */
    private function __construct() {}


    /**
     * Handle Dotenv
     *
     * @param string $app_root_path
     * @return void
     */
    public static function handle(string $app_root_path)
    {
        // Load the Dotenv from(https://github.com/vlucas/phpdotenv)
        $dotenv = vlucasDotenv::createImmutable($app_root_path);
        $dotenv->load();
    }
}