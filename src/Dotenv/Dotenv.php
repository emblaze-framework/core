<?php

namespace Emblaze\Dotenv;

class Dotenv
{
    /**
     * Dotenv constructor
     */
    private function __construct() {}


    public static function handle(string $app_root_path)
    {
        // Load the Dotenv from(https://github.com/vlucas/phpdotenv)
        $dotenv = Dotenv\Dotenv::createImmutable($app_root_path);
        $dotenv->load();
    }
}