<?php

namespace Psr\Factories\Test\SimpleContainer;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public function __construct(
        /**
         * @var array<string,Closure>
         */
        private array $services,
        /**
         * @var array<string,array<Closure>>
         */
        private array $extensions
    ) {
    }

    private array $instances = [];

    public function get(string $id): mixed
    {
        if (!isset($this->instances[$id])) {
            if (!isset($this->services[$id])) {
                throw new NotFoundException("Service not found: $id");
            }

            $instance = $this->services[$id]($this);

            foreach ($this->extensions[$id] ?? [] as $extension) {
                $instance = $extension($this, $instance);
            }

            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
