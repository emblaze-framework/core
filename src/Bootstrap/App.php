<?php

namespace Emblaze\Bootstrap;

use Emblaze\File\File;
use Emblaze\Http\Server;
use Emblaze\Http\Request;
use Emblaze\Router\Route;
use Emblaze\Cookie\Cookie;
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

        // load dotenv nad inject the App ROOT path.
        Dotenv::handle(ROOT);
       
        // $app var is now App intance
        self::$app = $this;

         // Register Whoops
         Whoops::handle();
        
         // Start Session
         Session::start();

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
        // Handle Request
        $this->request::handle();
        
        // Require all files from routes directory
        File::require_directory('routes');

        // Handle Routers
        $data =  $this->route::handle();

        // Outpute Response
        $this->response::output($data);

    }

    // Bind Some Custom Class Here?
    public function bind()
    {
        
    }
}