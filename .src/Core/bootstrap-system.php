```php
// public/index.php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->run();
} catch (Throwable $e) {
    App::handleException($e);
}

// bootstrap/app.php
$app = new Application(dirname(__DIR__));

// Register service providers
$app->register(new DatabaseServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new AuthServiceProvider());
$app->register(new MailServiceProvider());
$app->register(new QueueServiceProvider());

return $app;

// src/Core/Application.php
class Application {
    private Container $container;
    private Router $router;
    private string $basePath;
    private array $providers = [];

    public function __construct(string $basePath) {
        $this->basePath = $basePath;
        $this->container = new Container();
        $this->bootCore();
    }

    public function run(): void {
        // Load environment variables
        (new EnvLoader())->load($this->basePath . '/.env');

        // Load configuration
        Config::load($this->basePath . '/config');

        // Boot service providers
        $this->bootProviders();

        // Start session
        $this->container->get(SessionManager::class)->start();

        // Handle request
        $response = $this->container
            ->get(Router::class)
            ->dispatch(Request::createFromGlobals());

        $response->send();
    }

    public function register(ServiceProvider $provider): void {
        $this->providers[] = $provider;
        $provider->register($this->container);
    }

    private function bootCore(): void {
        $this->container->singleton(Application::class, fn() => $this);
        $this->container->singleton(Container::class, fn() => $this->container);
        $this->container->singleton(Router::class);
    }

    private function bootProviders(): void {
        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot($this->container);
            }
        }
    }

    public static function handleException(Throwable $e): void {
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        }

        $handler = new ExceptionHandler();
        $handler->handle($e)->send();
    }
}

// src/Core/ServiceProvider.php
abstract class ServiceProvider {
    abstract public function register(Container $container): void;
    
    public function boot(Container $container): void {}
}

// Example Service Provider
class DatabaseServiceProvider extends ServiceProvider {
    public function register(Container $container): void {
        $container->singleton(PDO::class, function() {
            return new PDO(
                Config::get('database.dsn'),
                Config::get('database.username'),
                Config::get('database.password'),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        });
    }
}
```

Want to see middleware implementation next?