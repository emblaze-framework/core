<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Bootstrap;

use Emblaze\File\File;
use Emblaze\Http\Request;
use Emblaze\Router\Route;
use Emblaze\Http\Response;
use Emblaze\Session\Session;
use Emblaze\Container\Container;
use Emblaze\ServiceProvider\Provider;

class App extends Container
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
     * Providers
     *
     * @var Route $request
     */
    public Provider $provider;

    /**
     * App constructor
     * 
     * @return void
     */
    public function __construct() {

        // Instantiate Cookie CsrfToken
        new \Emblaze\Cookie\CsrfToken();
       
        // Start Session
        Session::start();

        // $app is now App intance
        self::$app = $this;

        // Instantiate providers
        $this->provider = new Provider();

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
         * Handle services providers
         */
        $this->provider::handle();
        
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

}