<?php
namespace SilexRad\AutoTemplate\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

class AutoTemplateProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        $app['auto_template.render'] = $app->protect(function($data=array()) use ($app) {
            function dasherize($string) {
                return strtolower(ltrim(preg_replace('/([A-Z])/', '-$1', $string), '-'));
            };
            $controller = $app['request']->attributes->get('_controller');
            list($service, $method) = explode(':', $controller);
            $directory = dasherize(preg_replace('/Controller$/', '', $service));
            $file = dasherize($method);
            return $app['twig']->render("$directory/$file.twig", $data);
        });
    }

    public function boot(Application $app) {
    }
}