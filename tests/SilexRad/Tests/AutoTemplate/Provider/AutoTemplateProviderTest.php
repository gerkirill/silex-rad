<?php
namespace SilexRad\Tests;

use Silex\Application;
use SilexRad\AutoTemplate\Provider\AutoTemplateProvider;
use SilexRad\AutoRoute\Provider\AutoRouteProvider;
use Silex\Provider\TwigServiceProvider;
use SilexRad\Tests\Controller\MyTestController;
use Symfony\Component\HttpFoundation\Request;

class AutoTemplateProviderTest extends \PHPUnit_Framework_TestCase {
    public function testRouteWorks() {
        $app = new Application();
        $app->register(new AutoRouteProvider());
        $app->register(new AutoTemplateProvider());
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => array(__DIR__.'/../../template')
        ));
        $app['MyTestController'] = $app->share(function() use ($app) {
            return new MyTestController($app);
        });
        $request = Request::create('/my-test/test-template');
        $this->assertEquals('test is ok', $app->handle($request)->getContent());
        
    }

}