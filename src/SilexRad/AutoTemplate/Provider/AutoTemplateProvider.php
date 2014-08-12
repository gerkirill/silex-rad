<?php
namespace SilexRad\AutoTemplate\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use SilexRad\Provider\SilexRadProvider;
use SilexRad\ServiceNameConverterInterface;

class AutoTemplateProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        $app->register(new SilexRadProvider());
        $app['auto_template.render'] = $app->protect(function($data=array()) use ($app) {
            $controller = $app['request']->attributes->get('_controller');
            /** @var ServiceNameConverterInterface $converter */
            $converter = $app['silex_rad.service_name_converter'];
            $file = $converter->controllerToTemplate($controller);
            return $app['twig']->render($file, $data);
        });
    }

    public function boot(Application $app) {
    }
}