<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Session;

use Emblaze\Http\Request;

class CsrfToken 
{

    /**
     * Csrf Token Name
     *
     * @var string
     */
    public static string $csrf_name = '';

    /**
     * CsrfToken Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        self::$csrf_name = config('app.xsrf_name');
    }

    /**
     * Generate New Csrf Token
     *
     * @return void
     */
    public static function generate()
    {
        // generate the token
        $token = base64_encode(openssl_random_pseudo_bytes(32));
        return Session::set(self::$csrf_name, $token);
    }

    /**
     * Check or Validate the csrf token submitted by the user client
     *
     * @return void
     */
    public static function check()
    {
        // Check if the session has csrf token
        if(!Session::has(self::$csrf_name)) {
            throw new \Exception("Invalid token, or token expired.");
        }

        // Check if the post request has csrf_name = "csrf_token"
        if(!Request::value(self::$csrf_name)) {
            // Invalid token, or token expired.
            throw new \Exception("Invalid token, or token expired.");
        }

        // Check if the session csrf token is equal to user client request
        if(Session::get(self::$csrf_name) === Request::value(self::$csrf_name)) {
            return true;
        }

        return false;
    }

    /**
     * Get the generated csrf token <input> fields
     *
     * @return mixed
     */
    public static function get()
    {
        return sprintf('<input type="hidden" name="%s" value="%s">', 
                        self::$csrf_name,
                        self::generate());
    }
}