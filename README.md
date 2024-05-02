# PSR Factories

This repository describes and demonstrates an possible approach to standardizing service factories among frameworks and libraries.

It is an alternative to [standard service providers](https://github.com/container-interop/service-provider), which were attempted, but never widely adopted.

DI containers take drastically different approaches to implementation: ranging from simpler containers based on callables/closures, to more complex containers that rely on code-generation. The past proposal worked well for simpler containers, but created a performance bottleneck for containers that rely on complex code-generation techniques for optimizations.

Unlike the past proposal, this one attempts to standardize only how services are created, and not how they get registered by the dependency injection container framework or library.

**This is an early draft and a sandbox for experimentation.**

## Working Examples

Included in this repository is:

```
/src                     The attributes associated with the proposed standard
/test                    
  /src                   Working examples:
    /Example             - A use-case example with a UserFactory class and mock dependencies
    /Model               - A reflection-based model parsing the service/extension attributes
    /SimpleContainer     - A basic callable-based PSR-11 service container based on the Model
  test.php               Test coverage for the Model and SimpleContainer example code
```

See `test/src/Example/UserFactory.php` for a basic use-case example.

Use `composer test` to run the test.

## Attributes

To avoid specifying container implementation details, this proposal takes a data-driven approach, in which plain PHP **Factory** classes expose public methods to create or extend logical **Services**, meaning any object/instance or plain value made available for dependency injection.

The public methods and parameters of factory classes use a set of standard attributes to provide metadata for the DI container - this metadata *names* the services defined and required by the factory methods in a *declarative* way, by specifying the service identifiers.

Note that the term "identifiers" refers to [standard entry identifiers](https://www.php-fig.org/psr/psr-11/#111-entry-identifiers) as defined by the PSR-11 standard.

### Service Declarations

The `Service` attribute designates a logical service (created by a factory method) for registration in a DI container.

If a return-type is present, the `Service` attribute may be used without a service identifier:

```php
class UserFactory
{
    #[Service]
    public function createUserService(): UserService
    {
        // ...
    }
}
```

In this example, the DI container will register the service using e.g. `UserService::class` as the service identifier.

If the return-type is absent or different from the required service identifier, the attribute may specify the service identifier explicitly:

```php
class UserFactory
{
    #[Service(UserServiceInterface::class)]
    public function createUserService(): UserService
    {
        // ...
    }
}
```

In this example, the DI container will register the service using e.g. `UserServiceInterface::class` as the service identifier, overriding the `UserService` return-type.

### Dependency Declarations

The `Inject` attribute applies to method parameters, and designates a specific service identifier for dependency injection by the DI container.

If a parameter-type is present, the `Inject` attribute may be used without a service identifier:

```php
class UserFactory
{
    #[Service]
    public function createUserService(UserRepository $repository): UserService
    {
        return new UserService($repository);
    }
}
```

In this example, the DI container will inject the service using e.g. `UserRepository::class` as the identifier of the required service.

If the parameter-type is absent or different from the required service identifier, the attribute may specify the service identifier explicitly:

```php
class UserFactory
{
    #[Service]
    public function createUserRepository(#[Inject("user.db")] PDO $db): UserRepository
    {
        return new UserRepository($db);
    }
}
```

In this example, the DI container will resolve the required service using `user.db` as the service identifier, overriding the `PDO` parameter-type.

### Extension Declarations

The `Extension` attribute designates an extension method, which extends, decorates, manipulates or replaces (at the time of service creation) an existing service in a DI container.

The `Extend` attribute applies to an extension method parameter, and designates the existing service to be injected. The extension method must return a suitable replacement service or value of the same type, or a supertype, of the existing service.

Exactly *one* parameter must be designated as the existing service to be injected:

```php
class DevelopmentLoggerFactory
{
    #[Extension]
    public function addDevelopmentLogger(#[Extend] LoggerInterface $existingLogger)
    {
        return new DevelopmentLogger($existingLogger);
    }
}
```

In this example, the DI container will inject the service using e.g. `LoggerInterface::class` as the identifier of the service being extended, and then replaces the existing service with the replacement service returned by the extension method.

If the parameter-type is absent or different from the required service identifier, the `Extend` attribute may specify the service identifer explicitly:

```php
class DevelopmentLoggerFactory
{
    #[Extension]
    public function addDevelopmentLogger(#[Extend("app.logger")] LoggerInterface $existingLogger)
    {
        return new DevelopmentLogger($existingLogger);
    }
}
```

In this example, the DI container will resolve the required service using `app.logger` as the identifier of the service being extended, and replaces the existing service with the returned replacement service.
