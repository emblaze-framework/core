<?php

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
if(! function_exists('view')) {
    function view($path,$data = []) {
        return Emblaze\View\View::render($path,$data);
    }
}

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
if(! function_exists('request')) {
    function request($key) {
        return Emblaze\Http\Request::value($key);
    }
}

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
if(! function_exists('request')) {
    function redirect($path) {
        return Emblaze\Url\Url::redirect($path);
    }
}

// Before Url::previous($path);, now you can use previous($path)
/**
 * Previous
 * 
 * @return mixed
 */
if(! function_exists('previous')) {
    function previous() {
        return Emblaze\Url\Url::previous();
    }
}

// Before Url::path($path);, now you can use url($path)
/**
 * Url path
 * 
 * @param string $path
 * @return mixed
 */
if(! function_exists('url')) {
    function url($path) {
        return Emblaze\Url\Url::path($path);
    }
}

// Before Url::path($path);, now you can use asset($path)
/**
 * Asset path
 * 
 * @param string $path
 * @return mixed
 */
if(! function_exists('asset')) {
    function asset($path) {
        return Emblaze\Url\Url::path($path);
    }
}

// now you can use dd($data) to dump data
/**
 * Dump and die
 * 
 * @return mixed
 */
if(! function_exists('dd')) {
    function dd($data) {
        dump($data);
        die();
    }
}

// Before Session::get($key);, now you can use session($key)
/**
 * Get session data
 * 
 * @param string $key
 * @return string $data
 */
if(! function_exists('session')) {
    function session($key) {
        return Emblaze\Session\Session::get($key);
    }
}

// Before Session::flash($key);, now you can use flash($key)
/**
 * Get session flash data
 * 
 * @param string $key
 * @return string $data
 */
if(! function_exists('flash')) {
    function flash($key) {
        return Emblaze\Session\Session::flash($key);
    }
}

// Before Database::links($current_page,$pages);, now you can use links($current_page,$pages)
/**
 * Show pagination links
 * 
 * @param string $current_page
 * @param string $pages
 * @return mixed
 */
if(! function_exists('links')) {
    function links($current_page,$pages) {
        return Emblaze\Database\Database::links($current_page,$pages);
    }
}

/**
 * Table auth
 * 
 * @param string $table
 * 
 * @return string
 */
if(! function_exists('auth')) {
    function auth($table) {
        $auth = Emblaze\Session\Session::get($table) ?: Emblaze\Cookie\Cookie::get($table);
        return Emblaze\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}

/**
 * Csrf Token Generator
 * 
 * @return mixed
 */
if(! function_exists('csrf')) {
    function csrf() {
        return Emblaze\Session\CsrfToken::get();
    }
}