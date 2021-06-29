<?php

namespace Emblaze\Http;

class Request
{
    public $code = 200;
    
    /**
     * Script Name
     * 
     * @var $script_name
     */
    private static $script_name;

    /**
     * Base url
     * 
     * @var $base_url
     */
    private static $base_url;

    /**
     * Url
     * 
     * @var $url
     */
    private static $url;

    /**
     * Full Url
     * 
     * @var $full_url
     */
    private static $full_url;

    /**
     * Query String
     * 
     * @var $url
     */
    private static $query_string;
    
    /**
     * Request constructor
     * 
     * @return void
     */
    public function __construct() {
        echo "Request has been called";
    }

    /**
     * Handle the Request
     * 
     * @return void
     */
    public static function handle()
    {
        // "SCRIPT_NAME" => "/public/index.php"
        static::$script_name = str_replace('\\','',dirname(Server::get('SCRIPT_NAME')));
     
        static::setBaseUrl();
        static::setUrl();
    }

    /**
     * Set base url
     * 
     * @return void
     */
    private static function setBaseUrl()
    {
        // http://example.web
        $REQUEST_SCHEME = Server::has('REQUEST_SCHEME') ? Server::get('HTTP_HOST') : 'http'; //-> e.g. http
        $protocol = $REQUEST_SCHEME . '://'; //-> e.g. http://

        $HTTP_HOST = Server::has('HTTP_HOST') ? Server::get('HTTP_HOST') : 'localhost'; //-> e.g. example.web
        $host = $HTTP_HOST;

        $script_name = static::$script_name; //-> e.g. /public

        static::$base_url = $protocol . $host . $script_name; //-> e.g. http://example.web
    }

    /**
     * Set url
     * 
     * @return void
     */
    private static function setUrl()
    {
        // e.g. "REQUEST_URI" => "/public/index.php" or "/public/index.php?id=1&name=John"
        $request_uri = urldecode(Server::get('REQUEST_URI'));

        // e.g static::$script_name = "/public"
        // now using preg_replace the "/public" will be remove from $request_uri
        // so the $request_uri will now be "/index.php" or "/index.php?id=1&name=John"
        $request_uri = preg_replace("#^" . static::$script_name . '#', '', $request_uri);

        // rtrim() function removes whitespace or other predefined characters from the right side of a string.
        $request_uri = rtrim($request_uri, '/');

        // set full_url
        static::$full_url = $request_uri;

        
        if(strpos($request_uri, '?') !== false) {
            list($request_uri, $query_string) = explode('?',$request_uri);
        }
        
        static::$url = $request_uri?:"/";
        static::$query_string = $query_string ?? null;
    }

    /**
     * Get base url
     * 
     * @return string
     */
    public static function baseUrl()
    {
        return static::$base_url;
    }

    /**
     * Get url
     * 
     * @return string
     */
    public static function url()
    {
        return static::$url;
    }

    /**
     * Get query string
     * 
     * @return string
     */
    public static function query_string()
    {
        return static::$query_string;
    }

    /**
     * Get full url
     * 
     * @return string
     */
    public static function full_url()
    {
        return static::$full_url;
    }

    /**
     * Get request method
     * 
     * @return string
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * Request method is a POST ?
     * 
     * @return bool
     */
    public static function isPost()
    {
        return static::method() === "POST";
    }

    /**
     * Request method is a GET ?
     * 
     * @return bool
     */
    public static function isGet()
    {
        return static::method() === "GET";
    }

    /**
     * Check that the request has the key
     * 
     * @param array $type
     * @param string $key
     * 
     * @return bool
     */
    public static function has($type,$key)
    {
        return array_key_exists($key,$type);
    }

    /**
     * Get the value from the request 
     *  
     * @param string $key
     * @param array $type
     * 
     * @return bool
     */
    public static function value($key,array $type = null)
    {
        $type = isset($type) ? $type : $_REQUEST;
        return static::has($type, $key) ? $type[$key] : null;
        
    }

    /**
     * Get single value from GET request
     * 
     * @param string $key
     * @return string $value
     */
    public static function get($key)
    {
        return static::value($key, $_GET);
    }

    /**
     * Get single value from POST request
     * 
     * @param string $key
     * @return string $value
     */
    public static function post($key)
    {
        return static::value($key, $_POST);
    }

    /**
     * Set value for request by the given key
     * 
     * @param string $key
     * @param string $value
     * 
     * @return string
     */
    public static function set($key,$value)
    {
        $_REQUEST[$key] = $value;
        $_GET[$key] = $value;
        $_POST[$key] = $value;

        return $value;
    }

    /**
     * Get previous request value
     * 
     * @return string
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER') ?? "";
    }

    /**
     * Get request all
     * 
     * @return array
     */
    public static function all()
    {
        return $_REQUEST ?? [];
    }

    /**
     * Get request Body
     * 
     * @return array
     */
    public static function getBody()
    {
        $body = [];
        if(static::isGet()) {
            foreach ($_GET as $key => $value) {
                // this will remove some invalid chars from value.
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if(static::isPost()) {
            foreach ($_POST as $key => $value) {
                // this will remove some invalid chars from value.
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}