<?php
namespace SilexRad\Tests;

use Silex\Application;
use SilexRad\AutoRoute\Provider\AutoRouteProvider;
use SilexRad\Tests\Controller\MyTestController;
use Symfony\Component\HttpFoundation\Request;

class AutoRouteProviderTest extends \PHPUnit_Framework_TestCase {
    public function testRouteWorks() {
        $app = new Application();
        $app->register(new AutoRouteProvider());
        $app['MyTestController'] = $app->share(function() use ($app) {
            return new MyTestController($app);
        });
        $request = Request::create('/my-test/test-method');
        $this->assertEquals('ok', $app->handle($request)->getContent());
    }

}