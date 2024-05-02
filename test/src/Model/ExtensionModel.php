<?php

namespace Psr\Factories\Test\Model;

use Psr\Factories\Extend;
use ReflectionMethod;
use Exception;

class ExtensionModel extends ComponentModel
{
    public readonly string $extended_id;

    public function __construct(ReflectionMethod $method, object $factory)
    {
        parent::__construct($method, $factory);

        $params = $method->getParameters();

        $extended_id = null;

        foreach ($params as $param) {
            $extend_attr = $param->getAttributes(Extend::class)[0] ?? null;

            if ($extend_attr) {
                if ($extended_id) {
                    throw new Exception("Conflicting Extend-attributes: only once service can be extended by a method");
                }

                $extend = $extend_attr->newInstance();

                $extended_id = $extend->id;
            }
        }

        if ($extended_id === null) {
            throw new Exception("Missing Extend-attribute: an Extension method must extend another service");
        }

        $this->extended_id = $extended_id;
    }
}
