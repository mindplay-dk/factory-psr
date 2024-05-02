<?php

namespace Psr\Factories\Test\Example;

class UserRepository
{
    public function __construct(
        public readonly CacheInterface $cache,
        public array $loggers = []
    )
    {
    }

    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }
}
