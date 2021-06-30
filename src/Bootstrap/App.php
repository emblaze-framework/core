<?php

namespace Emblaze\Bootstrap;

use Emblaze\File\File;
use Emblaze\Http\Server;
use Emblaze\Http\Request;
use Emblaze\Router\Route;
use Emblaze\Cookie\Cookie;
use Emblaze\Dotenv\Dotenv;
use Emblaze\Http\Response;
use Emblaze\Session\Session;
use Emblaze\Database\Database;
use Emblaze\Exceptions\Whoops;

class App 
{
    /**
     * Instance of the App
     * 
     * @var App
     */
    public static App $app;

    /**
     * Request
     *
     * @var Request $request
     */
    public Request $request;

    /**
     * Response
     * 
     * @var Response $response
     */
    private Response $response;

    /**
     * Route
     *
     * @var Route $request
     */
    public Route $route;

    /**
     * App constructor
     * 
     * @return void
     */
    public function __construct() {

        // Handle/Load dotenv
        // Inject the App ROOT path.
        Dotenv::handle(ROOT);

        // Register Whoops
        env('WHOOPS_ENABLED') ? Whoops::handle() : null;
       
        // Start Session
        Session::start();

        // $app var is now App intance
        self::$app = $this;

        // Instantiate new Request
        $this->request = new Request();

        // Instantiate new Response
        $this->response = new Response();

        // Instantiate new Route & inject the Request, Response
        $this->route = new Route($this->request, $this->response);
        
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run()
    {  
        /**
         * Handle request
         */
        $this->request::handle();
        
        /**
         * Require all files from routes directory
         * 
         * This will add all web.php get, post routes
         * and api routes.
         */
        File::require_directory('routes');

        /**
         * Handle Routers
         * 
         * $@return mixed $data 
         */
        $data = $this->route::handle();

       
        /**
         * Send the response to user
         */
        $this->response::output($data);

    }

    // Bind Some Custom Class Here?
    public function bind()
    {
        
    }
}