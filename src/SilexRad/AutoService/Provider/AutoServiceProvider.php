<?php
namespace SilexRad\AutoService\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

class AutoServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        
        $app['auto_service.name_formatter'] = $app->protect(function($serviceClass, $settings) use ($app) {
            if ($settings['add_namespace_to_service_name']) {
                $serviceName = str_replace('\\', '.', ltrim($serviceClass, '\\'));
            } else {
                $nsParts = explode('\\', $serviceClass);
                $serviceName = end($nsParts);
            }
            return $serviceName;
        });

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
            'add_namespace_to_service_name' => false,
            'file_extension' => '.php'
        ), $app['auto_service.default_options']);
    }

    private function registerServices($app, $serviceDirectory, $settings) {
        $serviceNamespace = $settings['namespace'];
        $serviceNamespaceNormalized 
            = strlen($serviceNamespace)
            ? sprintf('\\%s\\', trim($serviceNamespace, '\\'))
            : '';
        $addNamespaceToServiceName = $settings['add_namespace_to_service_name'];
        $extension = $settings['file_extension'];
        $extensionRegexp = sprintf('/%s$/', preg_quote($extension));
        $dir = new \DirectoryIterator($serviceDirectory);
        /* @var  $fileInfo \SplFileInfo */
        foreach($dir as $fileInfo) {
            if(!$fileInfo->isFile()) continue;
            if(!preg_match($extensionRegexp, $fileInfo->getBasename())) continue;

            $serviceClass = $serviceNamespaceNormalized . $fileInfo->getBasename($extension);
            $serviceName = $app['auto_service.name_formatter']($serviceClass, $settings);
            $app['auto_service.registrator']($serviceName, $serviceClass, $settings);
        }
    }
} 