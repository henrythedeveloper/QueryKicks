```php
// src/Core/Container/Container.php
class Container {
    private array $bindings = [];
    private array $instances = [];
    private array $resolving = [];

    public function bind(string $abstract, $concrete = null, bool $shared = false): void {
        $concrete = $concrete ?? $abstract;
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared
        ];
    }

    public function singleton(string $abstract, $concrete = null): void {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, $instance): void {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract) {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return $this->build($abstract);
            }
            throw new ContainerException("No binding found for $abstract");
        }

        $concrete = $this->bindings[$abstract]['concrete'];
        $object = is_callable($concrete) ? $concrete($this) : $this->build($concrete);

        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function build(string $concrete) {
        if (isset($this->resolving[$concrete])) {
            throw new ContainerException("Circular dependency detected: $concrete");
        }
        $this->resolving[$concrete] = true;

        try {
            $reflector = new ReflectionClass($concrete);
            
            if (!$reflector->isInstantiable()) {
                throw new ContainerException("Class $concrete is not instantiable");
            }

            $constructor = $reflector->getConstructor();
            if (is_null($constructor)) {
                return new $concrete;
            }

            $dependencies = array_map(
                fn($param) => $this->resolveDependency($param),
                $constructor->getParameters()
            );

            unset($this->resolving[$concrete]);
            return $reflector->newInstanceArgs($dependencies);
        } catch (Exception $e) {
            unset($this->resolving[$concrete]);
            throw $e;
        }
    }

    private function resolveDependency(ReflectionParameter $param) {
        if ($type = $param->getType()) {
            try {
                return $this->make($type->getName());
            } catch (Exception $e) {
                if ($param->isOptional()) {
                    return $param->getDefaultValue();
                }
                throw $e;
            }
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new ContainerException(
            "Cannot resolve dependency: " . $param->getName()
        );
    }
}

// Example Usage:
class UserService {
    private UserRepository $repository;
    private Mailer $mailer;

    public function __construct(UserRepository $repository, Mailer $mailer) {
        $this->repository = $repository;
        $this->mailer = $mailer;
    }
}

$container = new Container();

// Bind interfaces to implementations
$container->bind(UserRepository::class, MySqlUserRepository::class);

// Register singleton
$container->singleton(Mailer::class, function($container) {
    return new SmtpMailer(Config::get('mail'));
});

// Resolve with dependencies
$userService = $container->make(UserService::class);
```

Need the application bootstrap implementation next?