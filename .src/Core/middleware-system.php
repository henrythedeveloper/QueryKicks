```php
// src/Core/Middleware/MiddlewareInterface.php
interface MiddlewareInterface {
    public function handle(Request $request, Closure $next);
}

// src/Core/Middleware/AuthMiddleware.php
class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request, Closure $next) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        return $next($request);
    }
}

// src/Core/Middleware/CsrfMiddleware.php
class CsrfMiddleware implements MiddlewareInterface {
    public function handle(Request $request, Closure $next) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new SecurityException('Invalid CSRF token');
            }
        }
        return $next($request);
    }
}

// src/Core/Request.php
class Request {
    public array $query;
    public array $request;
    public array $files;

    public function __construct() {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;
    }

    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }
}

// Modified Router to use middleware
class Router {
    private array $middlewares = [];
    
    public function addMiddleware(string $name, MiddlewareInterface $middleware): void {
        $this->middlewares[$name] = $middleware;
    }

    private function runMiddleware(array $middlewareNames, Request $request): void {
        $middleware = array_reduce(
            array_reverse($middlewareNames),
            function($next, $name) {
                return function($request) use ($next, $name) {
                    return $this->middlewares[$name]->handle($request, $next);
                };
            },
            function($request) {
                return true;
            }
        );

        $middleware($request);
    }
}

// Usage in index.php
$router = new Router();

// Register middleware
$router->addMiddleware('auth', new AuthMiddleware());
$router->addMiddleware('csrf', new CsrfMiddleware());

// Add routes with middleware
$router->add('GET', '/cart', [CartController::class, 'show'], ['auth', 'csrf']);
```

Want to see the implementation of more middleware or move to error handling?