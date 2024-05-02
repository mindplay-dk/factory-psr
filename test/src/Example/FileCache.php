<?php

namespace Psr\Factories\Test\Example;

class FileCache implements CacheInterface
{
    public function __construct(
        public readonly string $path
    )
    {
    }
}
