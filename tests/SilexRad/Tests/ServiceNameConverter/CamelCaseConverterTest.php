<?php
namespace SilexRad\Tests\ServiceNameConverter;

use SilexRad\ServiceNameConverter\CamelCaseConverter;

class CamelCaseConverterTest extends \PHPUnit_Framework_TestCase {

    public function testClassToService() {
        $converter = new CamelCaseConverter();
        $serviceName = $converter->classToService('App\\Controller\\MySampleController');
        $this->assertEquals('MySampleController', $serviceName);
    }

    public function testControllerToTemplate() {
        $converter = new CamelCaseConverter();
        $templatePath = $converter->controllerToTemplate('MySampleController:myMethod');
        $this->assertEquals('my-sample/my-method.twig', $templatePath);
    }

    public function testUrlToController() {
        $converter = new CamelCaseConverter();
        $controller = $converter->urlToController('/my-sample/my-method');
        $this->assertEquals('MySampleController:myMethod', $controller);
    }

    public function testUrlToControllerDefaultMethod() {
        $converter = new CamelCaseConverter();
        $controller = $converter->urlToController('/my-sample');
        $this->assertEquals('MySampleController:index', $controller);
    }

}