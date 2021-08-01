<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Router;

use ReflectionClass;
use Emblaze\View\View;
use Emblaze\Http\Request;
use Emblaze\Bootstrap\App;
use Emblaze\Http\Response;
use Emblaze\Debug\Backtrace;
use Emblaze\Middleware\MiddlewareStack;

class Route
{
     /**
     * Self instance
     * 
     */
    private static $route;

    
    /**
     * Route container
     * 
     * @var array $routes
     */
    private static $routes = [];


    /**
     * Route Middleware
     * 
     * e.g. Route::middleware('Admin|Owner', function() {})
     * 
     * @var string $middleware
     */
    private static $routeMiddlewares;
    
    /**
     * Prefix
     * 
     * @var string $prefix
     */
    private static $prefix;
    
    /**
     * Request
     * 
     * @var Request $request
     */
    private static Request $request;

    /**
     * Response
     * 
     * @var Response $response
     */
    private static Response $response;


    /**
     * Route Name
     *
     * @var string
     */
    private static $name = '';

    /**
     * Code Line
     *
     * @var integer
     */
    private static $code_line = 0;

    /**
     * Route constructor
     * 
     * @return void
     */
    public function __construct(Request $request, Response $response) {
        self::$request = $request;
        self::$response = $response;

        self::$route = $this;
    }

    
    /**
     * Add route
     * 
     * @param string methods
     * @param string $uri
     * @param object|callback $callback
     */
    private static function add($methods, $uri, $callback)
    {

        static::route_warning_for_duplication($uri, $methods);
        
        $controller_path = self::$route->file_name($callback);
        
        $uri = trim($uri,'/');
        
        $uri = rtrim(static::$prefix . '/' . $uri, '/');

        $uri = $uri?:'/';

        foreach (explode('|', $methods) as $method) {
            
            // generate random name
            // $bytes = random_bytes(16);
            // $random_name = bin2hex($bytes);
            // static::$name = $random_name;

            static::$name = count(static::$routes) + 1;
            
            static::$routes[static::$name] = [
                'uri' => $uri,
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$routeMiddlewares,
                'active' => true,
                'status' => 'Active',
                'name' => static::$name,
                'config' => [
                    'controller_path' => $controller_path,
                    'controller_code_line' => static::$code_line,
                    'route_path' => Backtrace::get(2)->file,
                    'route_code_line' => Backtrace::get(2)->line,
                ]
            ];
        }

        return self::$route;

    }

    /**
     * Display a Route warning for duplication
     *
     */
    private static function route_warning_for_duplication($uri, $method = null)
    {
        foreach (static::$routes as $key => $value) {
            if($value['uri'] != $uri) {
                continue;
            }
            
            if($value['uri'] === $uri && $value['method'] == $method) {
                
                if(!config('app.debug')) {
                    break;
                }
                
                $warning = "[Warning] - Route duplication detected. The first registered route will be prioritize to load and the other will be ignored.";
                $info1 = '[Info] - The route <b>'.$method.'</b> method with URI: <b>'.$uri.'</b> is already exists and you are about to add the same METHOD with same URI.';
                $info2 = '[Info] - Duplicated route can be found at: <b>'.Backtrace::get(3)->file.'</b> on line <b>'.Backtrace::get(3)->line.'</b>';
                
                echo $warning.'<br/>';
                echo $info1.'<br/>';
                echo $info2.'<br/>';
                
                break;   
            }
        }
    }

    /**
     * Get the full file name of the controller path,
     * and also get the code_line.
     *
     * @param mixed $callback
     * @return void
     */
    private function file_name($callback)
    {
        // Notes: Need to update soon:
        // the code_line of callback e.g. SiteController@index or [SiteController::class, 'method']
        // should be the line from where the method is coded.

        if(is_callable($callback)) {
            
            // The file name shoud be the the routes file.
            
            static::$code_line = Backtrace::get(3)->line;// Output e.g. 91
            
            $file_path = Backtrace::get(3)->file; // Output e.g. /Users/reymarkdivino/Desktop/PHP-MVC/emblaze/emblaze/routes/web.php
            
            return $file_path;
        }

        // use e.g. SiteController@index
        // like: Route::get('/home',SiteController@index)
        if(!is_array($callback) && strpos($callback,'@') !== false) {
            list($className, $method) = explode('@',$callback);
            
            $className = "App\Http\Controllers\\".$className;
            
            // if class is not found throw error
            if(!class_exists($className)) { 
                throw new \ReflectionException("class ".$className." is not found.");
            }

            // call the method then backtrace
            
        
            $a = new \ReflectionClass($className);

        }

        // OR
        // use e.g. [SiteController::class, 'method']
        // like: Route::get('/home',[SiteController::class, 'index'])
        if(is_array($callback)) {
            // className is the controller
            $className = $callback[0];

            if(class_exists($className)) {

                $a = new \ReflectionClass($className);
                
            } else {
                throw new \ReflectionException("Class ".$className." is not found.");
            }
        }

       
        return $a->getFileName();
        // Output: e.g. C:\xampp7\htdocs\develop\Internal\Database\DB\InternalDB.php
    }

    /**
     * Disabled route
     *
     */
    public function disable() 
    {
        
        static::$routes[static::$name]['active'] = false;
        static::$routes[static::$name]['status'] = 'Disabled';

        return self::$route;
    }

    /**
     * Enable route
     *
     */
    public function enable() 
    {
        static::$routes[static::$name]['active'] = true;
        static::$routes[static::$name]['status'] = 'Active';

        return self::$route;
    }

    /**
     * This will change the key_name from static::$routes[key_name]
     * and also update the 'name' from routes value
     *
     * @param string $name
     */
    public function name($key_name = '')
    {
        $key_name = trim($key_name);
        
        // If the name is already been set on $routes[] array
        if(array_key_exists($key_name, static::$routes)) {
            $msg = 'Duplicated route name:`'.$key_name.'` has been found at '.Backtrace::first()->file.' at line: '.Backtrace::first()->line;
            throw new \Exception($msg);
        }
        
        if($key_name != '') {
            
            // reference the value to $item var
            $item = static::$routes[static::$name];

            // Set the $item value into new routes with new key_name
            static::$routes[$key_name] = $item;

            // unset/remove the old routes item.
            unset(static::$routes[static::$name]);

            // also update the 'name' value from that routes.
            static::$routes[$key_name]['name'] = $key_name;

        }

        return self::$route;
    }

    /**
     * Add new GET route
     * 
     * @param string $uri
     * @param object|callback $callback
     */
    public static function get($uri, $callback)
    {
        return static::add('GET', $uri, $callback);
    }

    /**
     * Add new POST route
     * 
     * @param string $uri
     * @param object|callback $callback
     */
    public static function post($uri, $callback)
    {
        return static::add('POST', $uri, $callback);
    }

    /**
     * Add new any e.g. GET|POST route
     * 
     * @param string $uri
     * @param object|callback $callback
     */
    public static function any($uri, $callback)
    {
        return static::add('GET|POST', $uri, $callback);
    }

    /**
     * Set prefix for routing
     * 
     * @param string $prefix
     * @param callback $callback
     */
    public static function prefix($prefix, $callback)
    {
        $parent_prefix = static::$prefix;

        static::$prefix .= '/' . trim($prefix,'/');

        if(is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException("Please provide valid callback function");
        }

        static::$prefix = $parent_prefix;
    }

    /**
     * Set middleware for route group
     * 
     * @param string $middleware
     * @param callback $callback
     */
    public static function middleware_group($middleware, $callback)
    {
        $parent_middleware = static::$routeMiddlewares;

        static::$routeMiddlewares .= '|' . trim($middleware,'|');

        if(is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException("Please provide valid callback function");
        }

        // Reset the routeMiddlewares after call_user_func($callback) is triggered
        static::$routeMiddlewares = $parent_middleware;
    }

    public function middleware($middleware)
    {
        $parent_middleware = static::$routes[static::$name]['middleware'];

        $parent_middleware .= '|' . trim($middleware,'|');

        // static::$routeMiddlewares = $parent_middleware;
        
        static::$routes[static::$name]['middleware'] = $parent_middleware;
        
    }

    /**
     * Handle the request and match the routes
     * 
     * @return mixed
     */
    public static function handle()
    {
        // this will get the current requested uri e.g. /users/1/edit
        $uri = Request::url();
        
        // manipulate params e.g. /users/{params_id}/edit or /users/{params1}/{params2}/save
        foreach(static::$routes as $route) {
            $matched = true;
            
            $replace_with = "/(.*?)";
            
            // preg_replace will replace the "users/{params_id}/edit" into e.g. "users/(.*?)/edit"
            $route['uri'] = preg_replace('/\/{(.*?)}/',$replace_with, $route['uri']);
            
            // e.g. #^/users/(.*?)/edit$#
            $route['uri'] = '#^' . $route['uri'] . '$#';
            
            // preg_match will find the matched routes
            if(preg_match($route['uri'], $uri, $matches)) {
                /**
                 * $matches will now be e.g. 
                 * array[
                 *      0=>"/users/1/edit"
                 *      1=>"1"
                 * ]
                 */

                // Returns the shifted value, or null if array is empty or is not an array. 
                array_shift($matches);

                $params = array_values($matches);
                
                foreach($params as $param) {
                    if(strpos($param, '/')) {
                        $matched = false;
                    }
                }

                // Check if the $route['method'] is not equal to current client user Request method request.
                if($route['method'] != Request::method()) {
                    $matched = false;
                }

                if($matched == true) {
                    return static::invoke($route,$params);
                }
            }
        }

        return View::render('errors.404');
        // die("Route not found.");
        // throw new \Exception("This ".$uri. " route is not found. You should create a Route for this on /routes folder");
    }

    /**
     * Invoke the route
     * 
     * @param array $route
     * @param array $params
     */
    public static function invoke($route, $params = [])
    {

        // Check if the route is active
        if(!$route['active']) {
            echo 'Sorry, This route is currently disabled';
            die();
        }
        
        /** 
         * EXECUTE GLOBAL MIDDLEWARE FIRST BEFORE TRIGGERING THE other routes middleware
        */
        // static::executeGlobalMiddleware_v2();
        static::executeMiddlewareStack(\App\Http\HttpCore::$globalMiddleware, static::$request);
        
        /** 
         * EXECUTE ROUTE MIDDLEWARE FIRST BEFORE CALLING CONTROLLER CALLBACK
        */
        static::executeRouteMiddleware($route, static::$request);

        // -----------------------------------------------------------


        $callback = $route['callback'];

        
        if(is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        // use e.g. SiteController@index
        // like: Route::get('/home',SiteController@index)
        if(!is_array($callback) && strpos($callback,'@') !== false) {
            list($className, $method) = explode('@',$callback);
            
            $className = "App\Http\Controllers\\".$className;

            // if class is not found throw error
            if(!class_exists($className)) { 
                throw new \ReflectionException("class ".$className." is not found.");
            }
        
            $object = new $className();

            if(!method_exists($object, $method)) {
                throw new \ReflectionException("The method ".$method." is not exists at ".$className);
            }

            // Before calling the controller method we need to check/build what is the required parameters from that method.
            $params = static::buildMethodParameters($className, $method, $params);
            
            return call_user_func_array([$object, $method], $params);

        }

        // OR
        // use e.g. [SiteController::class, 'method']
        // like: Route::get('/home',[SiteController::class, 'index'])
        if(is_array($callback)) {
            // className is the controller
            $className = $callback[0];
            $method = $callback[1];           

            if(class_exists($className)) {
               
                // instantiate the Controller.
                $object = new $className();
                
                if(method_exists($object, $method)) {

                    // Before calling the controller method we need to check/build what is the required parameters from that method.
                    $params = static::buildMethodParameters($className, $method, $params);

                    // Trigger the method from $className and pass $params
                    return call_user_func_array([$object, $method], $params);
                } else {
                    throw new \ReflectionException("The method ".$method." is not exists at ".$className);    
                }
            } else {
                throw new \ReflectionException("Class ".$className." is not found.");
            }
        }
      
    }
    
    /**
     * This will build the parameters of controller method.
     * It means we will automatically injected required services provider into contoller method,
     * if that method needs our services provider
     *
     * @param string $className
     * @param mixed $controllerMethod
     * @param array $params
     * @param mixed $request
     * @param mixed $response
     * @return mixed
     */
    protected static function buildMethodParameters($className, $controllerMethod, $params = [])
    {
        // dump($controllerMethod);
        // Create a reflection of the class
        $reflector = new ReflectionClass($className);

        $methods = $reflector->getMethods();
        
        foreach($methods as $method) {

            if($method->name === $controllerMethod) {
                
                // Parameters of Controller Method.
                $methodParameters = $method->getParameters();

                // inf the parameters is empty or null
                if(!$methodParameters) {
                    return $params;
                }

                foreach($methodParameters as $dependency) {
                    
                    // $name = $dependency->name;

                    $position = $dependency->getPosition();

                    $type = $dependency->getType();
                    
                    if(!is_null($type)) {
                        $class = $type->getName(); 
                       
                        // Emblaze Request and Response should always be automatically added to params. 
                        // this is a given params to controller method.
                        // if Emblaze\Http\Request 
                        // This is automatically injected,
                        if($class === 'Emblaze\Http\Request') {
                            $params[$position] = static::$request;

                            continue;
                        }

                        if($class === 'Emblaze\Http\Response') {
                            $params[$position] = static::$response;

                            continue;
                        }

                        // check if the $class is not yet added to your container,
                        if(!App::$app->get($class)) {
                            throw new \Exception('This '.$class.' is not yet added to your container, please bind it first.');
                        }

                        // resolve the class and get the instance.
                        $resolveClass = App::$app->resolve($class);
                        
                        // set the resolve class instance to its controller method parameter position
                        $params[$position] = $resolveClass;

                    }                    
                }
                
                break;
            }
        }
        
        return $params;
    }
    

    
    /**
     * Execute routes middleware
     * 
     * @param Request $request
     * @param array $routes
     */
    protected static function executeRouteMiddleware($route = [])
    {
        $middlewareNames = explode('|',$route['middleware']);

        $newMiddlewareStack = [];
        
        foreach($middlewareNames as $middleware) {
            if($middleware != '') {
                $middleware = 'App\Http\Middleware\\'.$middleware;
                if(class_exists($middleware)) {
                    // $object = new $middleware;

                    // add the route middleware on newMiddlewareStack
                    $newMiddlewareStack[] = $middleware;
                    // trigger the handle method from Middleware
                    // call_user_func_array([$object, 'handle'],[]);
                    
                } else {
                    throw new \ReflectionException("class ".$middleware." does not exists.");
                }
            }
        }

        // This will return a new request from custom routes middlewares. e.g. Admin, Owner Middlewares.
        // $request = static::executeMiddlewareStack($newMiddlewareStack, $request);
        // return $request;
        return static::executeMiddlewareStack($newMiddlewareStack, static::$request);
    }

  
    /**
     * Execute middleware
     * 
     * @param array $routes
     */
    // protected static function executeMiddleware($route)
    // {
    //     $middlewareNames = explode('|',$route['middleware']);
        
    //     foreach($middlewareNames as $middleware) {
    //         if($middleware != '') {
    //             $middleware = 'App\Http\Middleware\\'.$middleware;
    //             if(class_exists($middleware)) {
    //                 $object = new $middleware;
    //                 // trigger the handle method from Middleware
    //                 call_user_func_array([$object, 'handle'],[]);
                    
    //             } else {
    //                 throw new \ReflectionException("class ".$middleware." does not exists.");
    //             }
    //         }
    //     }
    // }

    /**
     * This will execute Global Middleware stack from \App\Http\HttpCore;
     *
     * @return void
     */
    // protected static function executeGlobalMiddleware()
    // {
    //     // Get list of global middleware stack from \App\Http\HttpCore;
    //     $middlewares = \App\Http\HttpCore::$globalMiddleware;

    //     // Loop through middleware class
    //     foreach($middlewares as $middleware) {
    //         // if that class exists
    //         if(class_exists($middleware)) {
    //             // create new instance of that class
    //             $object = new $middleware;
    //             // trigger the handle method from Middleware
    //             call_user_func_array([$object, 'handle'],[]);
    //         } else {
    //             throw new \ReflectionException("class ".$middleware." does not exists.");
    //         }
    //     }
    // }

   
     /**
      * This will execute Global Middleware stack from \App\Http\HttpCore;
      * and can be used to execute custom routes middlewares
      *
      * @param array $middlewares
      * @param Request $request
      * @return mixed
      */
    protected static function executeMiddlewareStack($middlewares = [])
    {
        // Get list of global middleware stack from \App\Http\HttpCore;
        // $middlewares = \App\Http\HttpCore::$globalMiddleware;

        // Reverse the array of middlewares so that it will run from first to end.
        $middlewares = array_reverse($middlewares, true);
        
        // new intance ofMiddlewareStack
        $mwStack = new MiddlewareStack();

        // Loop through middleware classes and add it to MiddlewareStack
        foreach($middlewares as $middleware) {
            // if that class exists
            if(class_exists($middleware)) {
                // add new middleware stack
                $mwStack->add(new $middleware());
            } else {
                throw new \ReflectionException("class ".$middleware." does not exists.");
            }
        }
        
        // handle middleware stack and and inject the users static::$request
        // $request = $mwStack->handle($request);
        // return $request;
        return $mwStack->handle(static::$request);
    }

    /**
     * Get all routes
     * 
     * @return array
     */
    public static function allRoutes()
    {
        return static::$routes;
    }
}