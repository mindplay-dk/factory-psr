<?php

namespace Psr\Factories\Test\Model;

use Exception;
use ReflectionMethod;
use ReflectionNamedType;

class ServiceModel extends ComponentModel
{
    public readonly string $id;

    public function __construct(?string $id, ReflectionMethod $method, object $factory)
    {
        parent::__construct($method, $factory);
        
        if ($id) {
            $this->id = $id;
        } else {
            $return_type = $method->getReturnType();

            if ($return_type instanceof ReflectionNamedType) {
                if ($return_type->isBuiltin()) {
                    $param_name = $return_type->getName();

                    throw new Exception("Cannot infer service ID for method {$method->getName()}: built-in type {$param_name} cannot be used as a service ID");
                }

                $this->id = $return_type->getName();
            } else {
                throw new Exception("Cannot infer service ID for method {$method->getName()}: missing return type hint");
            }
        }
    }
}
