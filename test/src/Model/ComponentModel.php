<?php

namespace Psr\Factories\Test\Model;

use Exception;
use Psr\Factories\Extend;
use Psr\Factories\Inject;
use ReflectionMethod;
use ReflectionNamedType;

abstract class ComponentModel
{
    /**
     * @var array<string>
     */
    public readonly array $dependencies;

    public readonly ReflectionMethod $method;

    public function __construct(ReflectionMethod $method)
    {
        $dependencies = [];
        
        $params = $method->getParameters();

        foreach ($params as $param) {
            $inject_attr = $param->getAttributes(Inject::class)[0]
                ?? $param->getAttributes(Extend::class)[0]
                ?? null;

            if ($inject_attr) {
                $inject = $inject_attr->newInstance();

                $dependencies[] = $inject->id;

                continue;
            }

            $param_type = $param->getType();

            if ($param_type instanceof ReflectionNamedType) {
                if ($param_type->isBuiltin()) {
                    $param_name = "$".$param->getName();
                    $type = $param_type->getName();
                    $class = $method->getDeclaringClass()->getName();
                    $method = $method->getName();

                    throw new Exception("Cannot infer service ID for parameter {$param_name} of method {$class}::{$method}: built-in type {$type} cannot be used as a service ID");
                } else {
                    $dependencies[] = $param_type->getName();
                }
            } else {
                throw new Exception("Cannot infer service ID for method {$method->getName()}: missing type hint for parameter {$param->getName()}");
            }
        }

        $this->dependencies = $dependencies;
        $this->method = $method;
    }
}
