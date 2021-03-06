<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/*
|------------------------------------------------------
| Helper
|------------------------------------------------------
|
| This helper.php is now autoloaded files from composer.json:
"autoload": {
    "psr-4": {
      "App\\": "app/",
      "Emblaze\\": "Emblaze/src/"
    },
    "files": [
      "Emblaze/src/Helper/helper.php"
    ]
  },

  this means that when you use the require __DIR__.'/../vendor/autoload.php'; from public/index.php
  then this file helper.php will also included
*/


if(! function_exists('view')) {
    /**
     * This View render, you can now directly called a view() function to trigger the View::render
     * So if you are using View::render($path,$data), now you can use view($path,$data)
     */
    /**
     * View render
     * 
     * @param string $path
     * @param array $data
     * 
     * @return mixed
     */
    function view($path,$data = []) {
        return Emblaze\View\View::render($path,$data);
    }
}

if(! function_exists('viewblade')) {
    /**
     * This View Blade render, you can now directly called a viewblade() function to trigger the View::render
     * So if you are using View::bladeRender($path,$data), now you can use viewblade($path,$data)
     * 
     * We can use the standalone version of Laravel's Blade templating engine
     * e.g. https://github.com/jenssegers/blade
     */
    /**
     * View Blade render
     * 
     * @param string $path
     * @param array $data
     * 
     * @return mixed
     */
    function viewblade($path, $data = []) {
        return Emblaze\View\View::bladeRender($path,$data);
    }
}


if(! function_exists('request')) {
    /**
     * This Request get, you can now directly called a request() function to trigger the Request::value($key)
     * So if you are using Request::value($key);, now you can use request($key)
     */
    /**
     * Request get
     * 
     * @param string $key
     * @param mixed
     */
    function request($key) {
        return Emblaze\Http\Request::value($key);
    }
}
// if(! function_exists('request')) {
//     function request() {
//         return \Emblaze\Bootstrap\App::$request;
//     }
// }


if(! function_exists('redirect')) {
    /**
     * This Redirect, you can now directly called a redirect() function to trigger the Url::redirect($path);
     * So if you are using Url::redirect($path);, now you can use redirect($path)
     */
    /**
     * Redirect
     * 
     * @param string $path
     * @return mixed
     */
    function redirect($path) {
        return Emblaze\Url\Url::redirect($path);
    }
}


if(! function_exists('previous')) {
    // Before Url::previous($path);, now you can use previous($path)
    /**
     * Previous
     * 
     * @return mixed
     */
    function previous() {
        return Emblaze\Url\Url::previous();
    }
}


if(! function_exists('url')) {
    // Before Url::path($path);, now you can use url($path)
    /**
     * Url path e.g. user/login
     * OR route name e.g. user.login.index
     * 
     * @param string $path
     * @return mixed
     */
    function url($path_or_routeName) {
        
        if(array_key_exists($path_or_routeName, Emblaze\Router\Route::$routes)) {
            
            $route = (object)Emblaze\Router\Route::$routes[$path_or_routeName];
          
            return Emblaze\Url\Url::path($route->uri);    
        }

        return Emblaze\Url\Url::path($path_or_routeName);
    }
}

if(! function_exists('asset')) {
    // Before Url::path($path);, now you can use asset($path)
    /**
     * Asset path
     * 
     * @param string $path
     * @return mixed
     */
    function asset($path) {
        return Emblaze\Url\Url::path($path);
    }
}


if(! function_exists('dd')) {
    // now you can use dd($data) to dump data
    /**
     * Dump and die
     * 
     * @return mixed
     */
    function dd($data) {
        dump($data);
        die();
    }
}


if(! function_exists('session')) {
    // Before Session::get($key);, now you can use session($key)
    /**
     * Get session data
     * 
     * @param string $key
     * @return string $data
     */
    function session($key) {
        return Emblaze\Session\Session::get($key);
    }
}


if(! function_exists('flash')) {
    // Before Session::flash($key);, now you can use flash($key)
    /**
     * Get session flash data
     * 
     * @param string $key
     * @return string $data
     */
    function flash($key) {
        return Emblaze\Session\Session::flash($key);
    }
}


if(! function_exists('links')) {
    // Before Database::links($current_page,$pages);, now you can use links($current_page,$pages)
    /**
     * Show pagination links
     * 
     * @param string $current_page
     * @param string $pages
     * @return mixed
     */
    function links($current_page,$pages) {
        return Emblaze\Database\Database::links($current_page,$pages);
    }
}


if(! function_exists('auth')) {
    /**
     * Table auth
     * 
     * @param string $table
     * 
     * @return string
     */
    function auth($table) {
        $auth = Emblaze\Session\Session::get($table) ?: Emblaze\Cookie\Cookie::get($table);
        return Emblaze\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}

if(! function_exists('csrf')) {
    /**
     * Csrf Token Generator
     * 
     * @return mixed
     */
    function csrf() {
        // session
        // return Emblaze\Session\CsrfToken::get();
        // cookie
        return Emblaze\Cookie\CsrfToken::get();
    }
}


if(! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @return mixed
     */
    function env($key) {
        return $_ENV[$key];
    }
}

if(! function_exists('vd')) {
    /**
     * var_dump with pre tag
     *
     * @param  string  $key
     * @return mixed
     */
    function vd($data) {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }
}

if(! function_exists('app')) {
    /**
     * App instance
     *
     * @return mixed
     */
    function app() {
        return Emblaze\Bootstrap\App::$app;
    }
}

// if(! function_exists('appConfig')) {
//     /**
//      * App Config
//      *
//      * @return mixed
//      */
//     function appConfig($key = 'name') {
//         return app()->appConfig[$key];
//     }
// }

if(! function_exists('config')) {
    /**
     * App Config
     *
     * @return mixed
     */
    function config($keys = 'app.name') {
        return Emblaze\Config\Config::get($keys);
    }
}


if(! function_exists('get_page_load_time')) {
    /**
     * Get the page load time
     *
     * @return mixed
     */
    function get_page_load_time() {
        $end = number_format((microtime(true) - EMBLAZE_START),2);
        echo '<div style="position: fixed; padding: 5px;bottom: 0;width:100%;background:#6cf;background: linear-gradient(#504b45 0%, #3c3b37 100%);width: 100%; box-sizing: border-box;align-items: center;border-top-left-radius: 6px;border-top-right-radius: 6px;color: white;">This page loaded in ', $end, ' seconds</div>';
    }
}

if(! function_exists('route')) {
    /**
     * Get routes info
     *
     * @return object
     */
    function route($name = null) {
        return (object)Emblaze\Router\Route::$routes[$name];
    }
}



if(! function_exists('asset_url')) {
    /**
     * Get the page load time
     *
     * @return mixed
     */
    function asset_url($asset) {
        // get app config 
        // $app_config = Emblaze\File\File::require_file('config/app.php');
        // dump($app_config);
        // dump(Emblaze\File\File::path('test'));
        die();
    }
}