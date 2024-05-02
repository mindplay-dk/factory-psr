<?php

namespace Psr\Factories\Test\SimpleContainer;

use Psr\Container\ContainerInterface;
use Psr\Factories\Test\Model\FactoryModel;

class Context
{
    public function __construct()
    {}

    /**
     * @var array<string,Closure>
     */
    private $services = [];

    /**
     * @var array<string,array<Closure>>
     */
    private $extensions = [];

    public function addFactory(object $factory)
    {
        $model = new FactoryModel($factory);

        foreach ($model->services as $service) {
            $this->services[$service->id] = function (ContainerInterface $container) use ($factory, $service) {
                $params = [];

                foreach ($service->dependencies as $dependency) {
                    $params[] = $container->get($dependency);
                }

                return $service->method->invokeArgs($factory, $params);
            };
        }

        foreach ($model->extensions as $extension) {
            $this->extensions[$extension->extended_id][] = function (ContainerInterface $container, $service) use ($factory, $extension) {
                $params = [];

                foreach ($extension->dependencies as $dependency) {
                    $params[] = $dependency === $extension->extended_id
                        ? $service
                        : $container->get($dependency);
                }

                return $extension->method->invokeArgs($factory, $params);
            };
        }
    }

    public function createContainer(): Container
    {
        return new Container($this->services, $this->extensions);
    }
}
