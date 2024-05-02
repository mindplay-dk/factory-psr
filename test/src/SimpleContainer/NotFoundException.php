<?php

namespace Psr\Factories\Test\SimpleContainer;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(
        string $id
    ) {
        parent::__construct("Service not found: $id");
    }
}
