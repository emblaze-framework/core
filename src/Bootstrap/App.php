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

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class App 
{
    /**
     * App constructor
     * 
     * @return void
     */
    private function __construct() {}

    /**
     * Run the application
     * 
     * @return void
     */
    public static function run()
    {  
        // Register Whoops
        Whoops::handle();
        
        // Start Session
        Session::start();
    
        // Handle the request & Injection the SymfonyRequest createFromGlobals
        Request::handle(SymfonyRequest::createFromGlobals());
        
        // Require all files from routes directory
        File::require_directory('routes');

        // Handle Routers
        $data = Route::handle();

        Response::output($data);


        // Cookie::set('nameOfCookie2222', 'TheValueOfCookie2222');
        
        // Set session
        // Session::set('name','Rey Mark');

        // dump($_SESSION);
        
        // Session::remove('name');

        // dump($_SESSION);
        
        // dump(Session::all());
        
        // Session::destroy();

        // dump(Session::all());

        // echo "session flash: ".Session::flash('name');

        // dump(Session::all());
        // Cookie::set('nameOfCookie', 'TheValueOfCookie');
        // dump(Cookie::all());
        // Cookie::remove('language');
        // dump(Cookie::all());
        // throw new \Exception(message: "Test Error");
        // dump(Cookie::destroy());
        // dump(Server::path_info('https://example.com'));
        // dump(Server::all());
        
        // dump(Request::baseUrl());

        // dump(Request::baseUrl());
        // dump(Request::url());
        // dump(Request::query_string());
        // dump(Request::full_url());

        // dump(Request::method());
        // dump(Request::isPost());
        // dump(Request::isGet());
        // dump(Request::getBody());
    
        // dump(Request::get('name'));
        // dump(Request::post('name'));
        // dump(Request::all());
       
        // dump(Request::getBody());

        // echo File::path('routes/web.php');
        
        // File::require_file('routes/web.php');
        // dump(File::require_directory('routes'));
        
        
        // dump(Route::allRoutes());
        // dump(Route::handle());
        // Route::handle();
        
        // $db = Database::instance();
    }
}