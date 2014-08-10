<?php
namespace SilexRad\Tests;

use Silex\Application;
use SilexRad\AutoService\Provider\AutoServiceProvider;

class AutoServiceProviderTest extends \PHPUnit_Framework_TestCase {
    public function testServiceRegistered() {
        $app = new Application();
        $app->register(new AutoServiceProvider(), array('auto_service.directories' => array(
            __DIR__ . '/../../Service' => array(
                'namespace' => 'SilexRad\\Tests\\Service'
            )
        )));
        $app->boot();
        $this->assertInstanceOf('SilexRad\\Tests\\Service\\TestService', $app['TestService']);
        //$request = Request::create('/');
        //$app->get('/', function (Request $req) use ($request) {
        //    return $request === $req ? 'ok' : 'ko';
        //});
        //$this->assertEquals('ok', $app->handle($request)->getContent());
    }
}