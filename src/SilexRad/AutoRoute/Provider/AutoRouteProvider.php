<?php
namespace SilexRad\AutoRoute\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use SilexRad\Provider\SilexRadProvider;
use SilexRad\ServiceNameConverterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AutoRouteProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app->register(new SilexRadProvider());
    }

    public function boot(Application $app) {
        $app->match('/{url}', function($url) use ($app) {
            /** @var ServiceNameConverterInterface $converter */
            $converter = $app['rad.service_name_converter'];
            $controller = $converter->urlToController($url);
            $app['request']->attributes->set('_controller', $controller);
            list($controllerService, $method) = explode(':', $controller);
            if (!method_exists($app[$controllerService], $method)) {
                throw new NotFoundHttpException(
                    sprintf('Can not find controller %s:%s', $controllerService, $method)
                );
            }
            return $app[$controllerService]->$method($app['request']);
        })->value('url', 'default/index')->bind('rad_auto_route')->assert('url', '.*');
    }
}