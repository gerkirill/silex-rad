<?php
namespace SilexRad\AutoService\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use SilexRad\Provider\SilexRadProvider;
use SilexRad\ServiceNameConverterInterface;

class AutoServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app->register(new SilexRadProvider());
        $app['rad.service.registrator'] = $app->protect(function($serviceName, $serviceClass, $settings) use ($app) {
            $app[$serviceName] = $app->share(function() use ($app, $serviceClass) {
                return new $serviceClass($app);
            });
        });
        $app['rad.service.default_options'] = array();
        $app['rad.service.directories'] = array();
    }

    public function boot(Application $app) {
        $defaults = $this->getDefaults($app);
        foreach($app['rad.service.directories'] as $key => $value) {
            if (is_numeric($key)) {
                $serviceDirectory = $value;
                $settings = $defaults;
            } else {
                $serviceDirectory = $key;
                $settings = array_merge($defaults, $value);
            }
            $this->registerServices($app, $serviceDirectory, $settings);
        }
    }

    private function getDefaults($app) {
        return array_merge(array(
            'namespace' => '',
            'file_extension' => '.php'
        ), $app['rad.service.default_options']);
    }

    private function registerServices($app, $serviceDirectory, $settings) {
        $serviceNamespace = $settings['namespace'];
        $serviceNamespaceNormalized 
            = strlen($serviceNamespace)
            ? sprintf('\\%s\\', trim($serviceNamespace, '\\'))
            : '';
        $extension = '.' . ltrim($settings['file_extension'], '.');
        $extensionRegexp = sprintf('/%s$/', preg_quote($extension));
        $dir = new \DirectoryIterator($serviceDirectory);
        /** @var ServiceNameConverterInterface $serviceNameConverter */
        $serviceNameConverter = $app['rad.service_name_converter'];
        /* @var  $fileInfo \SplFileInfo */
        foreach($dir as $fileInfo) {
            if(!$fileInfo->isFile()) continue;
            if(!preg_match($extensionRegexp, $fileInfo->getBasename())) continue;

            $serviceClass = $serviceNamespaceNormalized . $fileInfo->getBasename($extension);
            $serviceName = $serviceNameConverter->classToService($serviceClass);
            // do not override service registered by hand
            if (!isset($app[$serviceName])) {
                $app['rad.service.registrator']($serviceName, $serviceClass, $settings);
            }
        }
    }
} 