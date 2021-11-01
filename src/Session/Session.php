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

class Session
{
    /**
     * Session constructor
     */
    private function __construct() {}

    /**
     * Session start
     * 
     * @return void
     */
    public static function start()
    {
        if(! session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_name(config('app.session_name'));
            session_start();
            var_dump("session_start");
        }
    }

    /**
     * Set new session
     * 
     * @param string $key
     * @param string $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $value;
    }

    /**
     * Check that session has the key
     * 
     * @param string $key
     * 
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get session by the given key
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public static function get($key)
    {
        return static::has($key) ? $_SESSION[$key] : null;
    }

    /**
     * Remove session by the given key
     * 
     * @param string $key
     * @return void
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Return all sessions
     * 
     * @return array
     */
    public static function all()
    {
        return $_SESSION;
    }

    /**
     * Destroy the session
     * 
     * @return void
     */
    public static function destroy()
    {
        foreach ($_SESSION as $key => $value) {
            static::remove($key);
        }
    }

    /**
     * Get flash session
     * 
     * @param string $key
     * @return string $value
     */
    public static function flash($key)
    {
        $value = null;
        if(static::has($key)) {
            $value = static::get($key);
            static::remove($key);
        }
        return $value;
    }

}