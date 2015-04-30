<?php
namespace SilexRad\Tests;

use Silex\Application;
use SilexRad\AutoService\Provider\AutoServiceProvider;

class AutoServiceProviderTest extends \PHPUnit_Framework_TestCase {
    public function testServiceAutoRegistered() {
        $app = new Application();
        $app->register(new AutoServiceProvider(), array('rad.service.directories' => array(
            __DIR__ . '/../../Service' => array(
                'namespace' => 'SilexRad\\Tests\\Service'
            )
        )));
        $app->boot();
        $this->assertInstanceOf('SilexRad\\Tests\\Service\\TestService', $app['TestService']);
    }

    public function testServiceManuallyRegistered() {
        $app = new Application();
        $app->register(new AutoServiceProvider(), array('rad.service.directories' => array(
            __DIR__ . '/../../ManualService' => array(
                'namespace' => 'SilexRad\\Tests\\ManualService'
            )
        )));
        $app['ManuallyRegisteredService'] = $app->share(function(){
            return new \SilexRad\Tests\ManualService\ManuallyRegisteredService('first');
        });
        $app->boot();
        $this->assertInstanceOf('SilexRad\\Tests\\ManualService\\ManuallyRegisteredService', $app['ManuallyRegisteredService']);
        $this->assertEquals(array('first'), $app['ManuallyRegisteredService']->getConstructorArguments(), 'Service planned for manual registration got wrong constructor parameter');
        $this->assertInstanceOf('SilexRad\\Tests\\ManualService\\AutoRegisteredService', $app['AutoRegisteredService']);
    }
}