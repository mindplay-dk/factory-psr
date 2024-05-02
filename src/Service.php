<?php

namespace Psr\Factories;

use Attribute;

/**
 * This attribute designates a method as a service factory method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Service
{
    public function __construct(
        /**
         * The service identifier.
         * 
         * Optional: if not provided, the return type of the method
         * will be used as the identifier.
         */
        public readonly ?string $id = null
    ) {
    }
}
