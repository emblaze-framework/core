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
use Emblaze\Http\Server;
use Emblaze\Http\Request;
use Emblaze\Router\Route;
use Emblaze\Cookie\Cookie;
use Emblaze\Http\Response;
use Emblaze\Session\Session;
use Emblaze\Database\Database;
use Emblaze\Container\Container;
use Emblaze\ServiceProvider\ServiceProviderInterface;

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
     * customClassStack
     * 
     * @var array
     */
    // public static $customClassStack = [];

    /**
     * Loaded Providers
     *
     * @var array
     */
    // protected $loadedProviders = [];

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

    // For example we if have a Routing Service, Event Service, Database Service.
    /**
     * Register Provider
     *
     * @param ServiceProviderInterface $provider
     * @return void
     */
    // public function registerProvider(ServiceProviderInterface $provider)
    // {
    //     if(!$this->providerHasBeenLoaded($provider)) {
    //         $provider->register($this);

    //         $this->loadedProviders[] = $provider;
    //     }

    // }

    /**
     * Check if the the Service Provider is has been loaded.
     *
     * @param ServiceProviderInterface $provider
     * @return void
     */
    // protected function providerHasBeenLoaded(ServiceProviderInterface $provider)
    // {
    //     return array_key_exists($provider,$this->loadedProviders);
    // }

    /**
     * Bind or Add new custom class that came from outside of the core framework
     *
     * @param array $newClassStack
     * @return void
     */
    // public function addCustomClass($newClassStack = [])
    // {
    //     foreach ($newClassStack as $newClass) {
    //         $customClassStack[] = $newClass;
    //     }
        
    //     vd($customClassStack);
    // }

    /**
     * Bind the CLASS/OBJECT into a container
     *
     * @return void
     */
    // public function bind($key,$value)
    // {
    //     $container = \Emblaze\Container\Container::getInstance();
    //     $container->testdata = "test444";
        
    //     $container->bind($key,$value);
    // }

    // public function resolveObject($key)
    // {
    //     $container = \Emblaze\Container\Container::getInstance();

    //     $container->resolve($key, []);
    // }

}