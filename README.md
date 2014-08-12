Silex RAD tools
===============

Set of dead simple Silex services to make development faster.

[![Build Status](https://travis-ci.org/gerkirill/silex-rad.svg?branch=master)](https://travis-ci.org/gerkirill/silex-rad)

Installation
------------

Using composer

```js
{"require": {"gerkirill/silex-rad": "dev-master"}}
```

Auto-Service
------------

Registers all the classes located in the given directories as services. The constructor of the service will receive single parameter - $app.

```php
$app->register(new \SilexRad\AutoService\Provider\AutoServiceProvider(), array(
    'rad.service.directories' => array(
        __DIR__ . '/../src/Service' => array('namespace' => 'Service')
    )
));
```

E.g. class MyTestService located in Service/MyTestService.php with namespace "Service" will be accessible with $app['MyTestService'].

Auto-Route
----------

Handles routes in a form of '/{controller}/{action}' automatically. Requires controllers to be classes registered as services. {action} part is optional and defaults to "index".

```php
$app->register(new \SilexRad\AutoRoute\Provider\AutoRouteProvider());
```

E.g. URL my-test/my-example will be processed with MyTest:myExample controller. The controller will be invoked with single parameter - $request. The url will match both GET and POST. If you need more precise route tuning - go create the route manually.

Auto-Template
-------------

Detects twig template name by the current controller and action. Requires controller to be a class registered as service.

```php
$app->register(new \SilexRad\AutoTemplate\Provider\AutoTemplateProvider());
```

In your controller you now can do like this:

```php
class MyTestController {
    private $app;
    ...
    public function myExample() {
        return $this->app['rad.template.render'](['hello' => 'world']);
    }    
}
```

And corresponding template would be my-test/my-example.twig under template directory configured for twig.

Extension points
----------------