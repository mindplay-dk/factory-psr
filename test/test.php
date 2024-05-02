<?php

use Psr\Factories\Test\Example\UserFactory;
use Psr\Factories\Test\Model\FactoryModel;
use Psr\Factories\Test\SimpleContainer\Context;
use Psr\Factories\Test\Example\UserRepository;
use Psr\Factories\Test\Example\FileCache;

use function mindplay\testies\{ ok, eq, test, run, configure };

require_once dirname(__DIR__) . '/vendor/autoload.php';

//configure()->throwExceptions();

function has_service(FactoryModel $model, string $id, array $dependencies): void
{
    $index = array_search($id, array_column($model->services, "id"));
    ok(isset($model->services[$index]), "service {$id} exists");
    eq($model->services[$index]->id, $id);
    eq($model->services[$index]->dependencies, $dependencies);
}

function has_extension(FactoryModel $model, string $id, array $dependencies): void
{
    $index = array_search($id, array_column($model->extensions, "extended_id"));
    ok(isset($model->extensions[$index]), "extension {$id} exists");
    eq($model->extensions[$index]->extended_id, $id);
    eq($model->extensions[$index]->dependencies, $dependencies);
}

test(
    "can create a factory model",
    function () {
        $factory = new UserFactory("/dev/null");

        $model = new FactoryModel($factory);

        has_service($model, "user.cache-path", []);
        has_service($model, "user.cache", []);
        has_service($model, UserRepository::class, ["user.cache", "loggers"]);
        has_service($model, "loggers", []);
        has_service($model, "user.logger", []);

        has_extension($model, "loggers", ["loggers", "user.logger"]);
    }
);

test(
    "can create services",
    function () {
        $context = new Context();

        $cache_path = "/dev/null";

        $context->addFactory(new UserFactory($cache_path));

        $container = $context->createContainer();

        ok($container->has("user.cache-path"));
        ok($container->get("user.cache-path") === $cache_path);
        ok($container->get(UserRepository::class) instanceof UserRepository);
        ok($container->get(UserRepository::class)->cache instanceof FileCache);
        ok($container->get("user.cache") instanceof FileCache);
        eq($container->get(UserRepository::class)->cache, $container->get("user.cache"));
    }
);

exit(run());
