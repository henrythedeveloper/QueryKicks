```php
// src/Core/Router.php
class Router {
    private array $routes = [];
    private array $middlewares = [];

    public function add(string $method, string $path, $handler, array $middleware = []): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $method, string $uri): void {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $method, $uri)) {
                $this->runMiddleware($route['middleware']);
                $this->executeHandler($route['handler']);
                return;
            }
        }
        
        throw new NotFoundException('Route not found');
    }

    private function matchRoute(array $route, string $method, string $uri): bool {
        return $route['method'] === $method && $route['path'] === $uri;
    }

    private function executeHandler($handler): void {
        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $controller = new $controller();
            $controller->$method();
        } elseif (is_callable($handler)) {
            $handler();
        }
    }
}

// public/index.php
require_once '../vendor/autoload.php';

$router = new Router();

// Define routes
$router->add('GET', '/', [ProductController::class, 'index']);
$router->add('GET', '/cart', [CartController::class, 'show'], ['auth']);
$router->add('POST', '/cart/add', [CartController::class, 'add'], ['auth']);
$router->add('POST', '/checkout', [CartController::class, 'checkout'], ['auth']);

// Handle request
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

Want me to show middleware implementation next?