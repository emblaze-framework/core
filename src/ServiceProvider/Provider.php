<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\ServiceProvider;

use Emblaze\File\File;
use Emblaze\Bootstrap\App;


class Provider
{
    /**
     * Handle Service Provider
     *
     * @return mixed
     */
    public static function handle()
    {
        // get the app config array
        $app_config = File::require_file('config/app.php');

        // get the providers stack from app_config array
        $providersStack = $app_config['providers'];

        // loop through the providers
        foreach ($providersStack as $provider) {

            // check if service provider class exists
            if(!class_exists($provider)) {
                throw new \Exception('Service provider can\'t be register. Class provider '.$provider.' are not exists');
            }

            // create new instance of service provider
            $providerInstance = new $provider;

            // trigger the register method and inject the App instance.
            $providerInstance->register(App::$app);
        }
    }
}