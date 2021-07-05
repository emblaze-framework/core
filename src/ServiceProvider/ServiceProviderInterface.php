<?php

namespace Emblaze\ServiceProvider;

use Emblaze\Bootstrap\App;

interface ServiceProviderInterface
{
    public function register(App $app);
}