<?php

namespace Psr\Factories\Test\Model;

use Psr\Factories\Extension;
use Psr\Factories\Service;
use ReflectionClass;
use ReflectionMethod;

class FactoryModel
{
    /**
     * @var array<string,ServiceModel>
     */
    public readonly array $services;

    /**
     * @var array<string,ExtensionModel>
     */
    public readonly array $extensions;

    public function __construct(object $factory)
    {
        $services = [];
        $extensions = [];

        $class = new ReflectionClass($factory);

        /**
         * @var ReflectionMethod[]
         */
        $methods = $class->getMethods();

        foreach ($methods as $method) {
            $attributes = $method->getAttributes();

            foreach ($attributes as $attribute) {
                $attribute = $attribute->newInstance();

                if ($attribute instanceof Service) {
                    $service = new ServiceModel($attribute->id, $method, $factory);
                    $services[] = $service;
                }

                if ($attribute instanceof Extension) {
                    $extension = new ExtensionModel($method, $factory);
                    $extensions[] = $extension;
                }
            }
        }

        $this->services = $services;
        $this->extensions = $extensions;
    }
}
