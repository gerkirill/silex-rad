<?php
namespace SilexRad\AutoService\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use SilexRad\Provider\SilexRadProvider;
use SilexRad\ServiceNameConverterInterface;

class AutoServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app->register(new SilexRadProvider());
        $app['auto_service.registrator'] = $app->protect(function($serviceName, $serviceClass, $settings) use ($app) {
            $app[$serviceName] = $app->share(function() use ($app, $serviceClass) {
                return new $serviceClass($app);
            });
        });

        if (!isset($app['auto_service.default_options'])) {
            $app['auto_service.default_options'] = array();
        }
        // for each configured directories - register classes as configured for the given directory
        if (!isset($app['auto_service.directories'])) {
            $app['auto_service.directories'] = array();
        }
    }

    public function boot(Application $app) {
        $defaults = $this->getDefaults($app);
        foreach($app['auto_service.directories'] as $key => $value) {
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
        ), $app['auto_service.default_options']);
    }

    private function registerServices($app, $serviceDirectory, $settings) {
        $serviceNamespace = $settings['namespace'];
        $serviceNamespaceNormalized 
            = strlen($serviceNamespace)
            ? sprintf('\\%s\\', trim($serviceNamespace, '\\'))
            : '';
        $extension = $settings['file_extension'];
        $extensionRegexp = sprintf('/%s$/', preg_quote($extension));
        $dir = new \DirectoryIterator($serviceDirectory);
        /** @var ServiceNameConverterInterface $serviceNameConverter */
        $serviceNameConverter = $app['silex_rad.service_name_converter'];
        /* @var  $fileInfo \SplFileInfo */
        foreach($dir as $fileInfo) {
            if(!$fileInfo->isFile()) continue;
            if(!preg_match($extensionRegexp, $fileInfo->getBasename())) continue;

            $serviceClass = $serviceNamespaceNormalized . $fileInfo->getBasename($extension);
            $serviceName = $serviceNameConverter->classToService($serviceClass);
            $app['auto_service.registrator']($serviceName, $serviceClass, $settings);
        }
    }
} 