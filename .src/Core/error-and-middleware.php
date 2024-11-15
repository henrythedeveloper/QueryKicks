```php
// src/Core/Exceptions/Handler.php
class ExceptionHandler {
    public function handle(Throwable $e): void {
        $this->logError($e);
        $this->renderError($e);
    }

    private function logError(Throwable $e): void {
        error_log(sprintf(
            "Error: %s\nFile: %s\nLine: %d\nTrace:\n%s",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    }

    private function renderError(Throwable $e): void {
        if ($e instanceof ValidationException) {
            http_response_code(422);
            echo json_encode(['errors' => $e->getErrors()]);
        } elseif ($e instanceof AuthenticationException) {
            http_response_code(401);
            header('Location: /login');
        } else {
            http_response_code(500);
            require 'views/errors/500.php';
        }
    }
}

// Additional Middleware
class RateLimitMiddleware implements MiddlewareInterface {
    private const MAX_REQUESTS = 100;
    private const TIME_WINDOW = 3600; // 1 hour

    public function handle(Request $request, Closure $next) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:$ip";
        
        $requests = cache()->get($key, 0);
        if ($requests >= self::MAX_REQUESTS) {
            throw new RateLimitException('Too many requests');
        }
        
        cache()->increment($key);
        cache()->expire($key, self::TIME_WINDOW);
        
        return $next($request);
    }
}

class LogMiddleware implements MiddlewareInterface {
    public function handle(Request $request, Closure $next) {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $startTime;
        $this->logRequest($request, $duration);
        
        return $response;
    }

    private function logRequest(Request $request, float $duration): void {
        $log = sprintf(
            "[%s] %s %s - %.2fms",
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getUri(),
            $duration * 1000
        );
        error_log($log);
    }
}

// Usage in index.php
try {
    $router = new Router();
    
    // Register middleware
    $router->addMiddleware('auth', new AuthMiddleware());
    $router->addMiddleware('csrf', new CsrfMiddleware());
    $router->addMiddleware('rate_limit', new RateLimitMiddleware());
    $router->addMiddleware('log', new LogMiddleware());
    
    // Define routes with middleware
    $router->add('POST', '/products', [ProductController::class, 'store'], 
        ['auth', 'csrf', 'rate_limit']);
    
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    (new ExceptionHandler())->handle($e);
}

// views/errors/500.php
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
</head>
<body>
    <h1>Server Error</h1>
    <?php if (env('APP_DEBUG')): ?>
        <p><?= htmlspecialchars($e->getMessage()) ?></p>
    <?php else: ?>
        <p>An unexpected error occurred. Please try again later.</p>
    <?php endif; ?>
</body>
</html>
```

Next step: Create configuration management system?