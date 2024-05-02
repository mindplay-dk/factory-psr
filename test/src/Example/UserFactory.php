<?php

namespace Psr\Factories\Test\Example;

use Psr\Factories\Extend;
use Psr\Factories\Extension;
use Psr\Factories\Inject;
use Psr\Factories\Service;

class UserFactory
{
    public function __construct(
        public readonly string $cache_path
    ) {}

    #[Service("user.cache-path")]
    public function getCachePath(): string
    {
        return $this->cache_path;
    }

    #[Service("user.cache")]
    public function createCache(): CacheInterface
    {
        return new FileCache($this->cache_path);
    }

    #[Service]
    public function createUserRepository(
        #[Inject("user.cache")] CacheInterface $cache,
        #[Inject("loggers")] array $loggers
    ): UserRepository
    {
        $repo = new UserRepository($cache);
        foreach ($loggers as $logger) {
            $repo->addLogger($logger);
        }
        return $repo;
    }

    #[Service("loggers")]
    public function initLoggers()
    {
        return [];
    }

    #[Extension]
    public function addLoggers(
        #[Extend("loggers")] array $loggers,
        #[Inject("user.logger")] LoggerInterface $user_logger
    ): array
    {
        return [...$loggers, $user_logger, new AnotherLogger()];
    }

    #[Service("user.logger")]
    public function createUserLogger(): LoggerInterface
    {
        return new SomeLogger();
    }
}
