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

Registers all the classes located in the given directories as services (non-recursively). The constructor of the service
will receive single parameter - $app. If you want to pass custom parameters to the constructor - just register the service
yourself with the name auto-service would use.

```php
$app->register(new \SilexRad\AutoService\Provider\AutoServiceProvider(), array(
    'rad.service.directories' => array(
        __DIR__ . '/../src/Service' => array('namespace' => 'Service')
    )
));
```

E.g. class Service\MyTestService located in Service/MyTestService.php will be accessible with $app['MyTestService'].
Config key 'rad.service.directories' contains associative array where keys are paths to the folders you services reside in.
The values are associative arrays with settings. Possible settings keys are:
 - file_extension - '.php' by default, you can change it e.g. to '.inc' if your services use that extension. Files with 
 other extensions will be skipped.
 - namespace - namespace you services are grouped under, e.g. "Services" or "MyVendor/MyProject/Services"

Default behaviour can be changed with settings $app['rad.service_name_converter'] and $app['rad.service.registrator'].
You can find more details on that under "Extension points" section.
 

Auto-Route
----------

Handles routes in a form of '/{controller}/{action}' automatically. Requires controllers to be classes registered as services. {action} part is optional and defaults to "index".

```php
// this built-in service provider is required to use auto-route
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
// here you can use AutoServiceProvider to automatically register controllers as services, or you'll have to do it manually
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