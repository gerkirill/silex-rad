<?php
namespace SilexRad\AutoRoute\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AutoRouteProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['auto_route.controller_resolver'] = $app->protect(function($controller, $action='index') use ($app) {
            function dashedToCamelCase($string) {
                return preg_replace_callback('%(-.)%', function($m) {
                    return ltrim(strtoupper($m[0]), '-');
                }, $string);
            };
            $action = dashedToCamelCase($action);
            $controllerService = ucfirst(dashedToCamelCase($controller)) . 'Controller';
            return array($controllerService, $action);
        });
    }

    public function boot(Application $app) {
        $app->match('/{controller}/{action}', function($controller, $action='index') use ($app) {
            
            list($controllerService, $action) = $app['auto_route.controller_resolver']($controller, $action);
            $app['request']->attributes->set('_controller', $controllerService . ':' . $action);
            if (!method_exists($app[$controllerService], $action)) {
                throw new NotFoundHttpException(
                    sprintf('Can not find controller %s:%s', $controllerService, $action)
                );
            }
            return $app[$controllerService]->$action($app['request']);
        })->value('action', 'index')->bind('auto_route');
    }
}