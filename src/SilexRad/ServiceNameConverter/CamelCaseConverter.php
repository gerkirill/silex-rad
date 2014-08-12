<?php
namespace SilexRad\ServiceNameConverter;

use SilexRad\ServiceNameConverterInterface;

class CamelCaseConverter implements ServiceNameConverterInterface{

    /**
     * Example: App\Controller\MySampleController => MySampleController
     */
    public function classToService($serviceClass) {
        $nsParts = explode('\\', $serviceClass);
        $serviceName = end($nsParts);
        return $serviceName;
    }

    /**
     * Example: MySampleController:myMethod => my-sample/my-method.twig
     */
    public function controllerToTemplate($controller) {
        list($service, $method) = explode(':', $controller);
        $directory = $this->camelCaseToDashed(preg_replace('/Controller$/', '', $service));
        $file = $this->camelCaseToDashed($method);
        return "$directory/$file.twig";
    }

    /**
     * Example: /my-sample/my-method => MySampleController:myMethod
     */
    public function urlToController($url) {
        $urlParts = explode('/', trim($url, '/'));
        $servicePart = empty($urlParts[0]) ? 'Default' : $urlParts[0];
        $methodPart = empty($urlParts[1]) ? 'index' : $urlParts[1];
        $method = $this->dashedToCamelCase($methodPart);
        $service = ucfirst($this->dashedToCamelCase($servicePart)) . 'Controller';
        return sprintf('%s:%s', $service, $method);
    }

    private function dashedToCamelCase($dashedString) {
        return preg_replace_callback('%(-.)%', function($m) {
            return ltrim(strtoupper($m[0]), '-');
        }, $dashedString);
    }

    private function camelCaseToDashed($camelCasedString) {
        return strtolower(ltrim(preg_replace('/([A-Z])/', '-$1', $camelCasedString), '-'));
    }
}