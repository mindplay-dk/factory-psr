<?php

namespace Psr\Factories;

use Attribute;

/**
 * This attribute designates a parameter for dependency injection.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Extend
{
    public function __construct(
        /**
         * Identifier of the service to inject and extend.
         */
        public readonly ?string $id
    ) {
    }
}
