<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\View;

use Emblaze\File\File;
use Jenssegers\Blade\Blade;
use Emblaze\Debug\Backtrace;
use Emblaze\Session\Session;

class View
{
    /**
     * View Constructor
     */
    private function __construct() {}


    public static function render($path, $data = [])
    {


        /**
         * flash errors that came from Emblaze\Validation\Validate
         */
        // $errors = Session::flash('errors');
        // $old = Session::flash('old');

        // add the errors and old into $data
        // $data = array_merge($data, [$errors, $old]);

        // Render using Blade Engine
        // return static::bladeRender($path, $data);

        // OR Render using our custom Render
        return static::viewRender($path, $data);

        
    }

    /**
     * Do you want Blade Engine?
     * We can use the standalone version of Laravel's Blade templating engine
     * e.g. https://github.com/jenssegers/blade
     * 
     * Render the view files using Blade Engine
     * 
     * @param string $path
     * @param array $data
     * @return string
     */
    public static function bladeRender($path, $data = [])
    {
        $view = File::path('views');
        $cache = File::path('storage/cache');

        $blade = new Blade($view, $cache);

        return $blade->make($path, $data)->render();
    }
     
     /**
      * Use our own custom render view
      * Render view file
      *
      * @param string $path
      * @param array $data
      * @return string
      */
    public static function viewRender($path,$data = [])
    {
        // e.g. View::render('admin.dashboard', $data); will be "views/admin/dashboard.php"
        // OR e.g. View::render('admin/dashboard', $data); will be "views/admin/dashboard.php"
        $path = 'views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.php';

        if(!File::exist($path)) {
            throw new \Exception("The view file ".ROOT."{$path} is not exists");
        }

        ob_start(); // start buffer
        extract($data); //-> this will include $data extract to view files.
        include File::path($path);
        $content = ob_get_contents();
        ob_end_clean(); // clean buffer

        return $content;
    }
}