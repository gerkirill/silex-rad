<?php
namespace SilexRad;

/**
 * Defines naming conventions for services, URLs and templates.
 */
interface ServiceNameConverterInterface {

    /**
     * Converts qualified class name to service identifier for DI container.
     * @param string $serviceClass Full class name, with namespace, e.g. App\Controller\MySampleController.
     * @return string Service identifier inside pimple DI container $app e.g. "MySampleController".
     * or "controller.my_sample"
     */
    public function classToService($serviceClass);

    /**
     *
     * @param string $controller Controller-as-Service identifier e.g. MySampleController:myMethod
     * @return string Path to twig template relative to the configured twig template folder,
     * e.g. my-sample/my-method.twig
     */
    public function controllerToTemplate($controller);

    /**
     * Converts URL to controller service name. Note: controller should be registered as a service.
     * @param string $url e.g. /my-sample/my-method
     * @return string e.g. MySampleController:myMethod
     */
    public function urlToController($url);
}