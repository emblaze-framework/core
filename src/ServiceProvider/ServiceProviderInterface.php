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

use Emblaze\Bootstrap\App;

interface ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @param App $app
     * @return void
     */
    public function register(App $app);
    
}