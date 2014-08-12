<?php
namespace SilexRad;

interface ServiceNameConverterInterface {
    public function classToService($serviceClass);
    public function controllerToTemplate($controller);
    public function urlToController($url);
}