<?php

namespace Psr\Factories;

use Attribute;

/**
 * This attribute designates a method as an extension factory method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Extension
{
}
