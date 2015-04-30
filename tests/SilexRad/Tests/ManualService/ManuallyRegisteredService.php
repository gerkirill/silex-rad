<?php
namespace SilexRad\Tests\ManualService;

class ManuallyRegisteredService {
    private $arguments;
    public function __construct($arg) {
        $this->arguments = func_get_args();
    }

    public function getConstructorArguments() {
        return $this->arguments;
    }
}