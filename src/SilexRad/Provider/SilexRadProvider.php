<?php
namespace SilexRad\Provider;

use Silex\Application;
use SilexRad\ServiceNameConverter\CamelCaseConverter;
use Silex\ServiceProviderInterface;

class SilexRadProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        if (isset($app['rad.registered'])) return;
        $app['rad.service_name_converter'] = $app->share(function(){
            return new CamelCaseConverter();
        });
    }

    public function boot(Application $app) {

    }
} 