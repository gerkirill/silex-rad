<?php
namespace SilexRad\Tests\Controller;

class MyTestController {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }
    public function testMethod() {
        return 'ok';
    }

    public function testTemplate() {
        return $this->app['auto_template.render'](array('result' => 'ok'));
    }
}