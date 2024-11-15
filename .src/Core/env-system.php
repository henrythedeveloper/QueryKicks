```php
// src/Core/Env/EnvLoader.php
class EnvLoader {
    public static function load(string $path): void {
        if (!file_exists($path)) {
            throw new EnvException(".env file not found");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = self::processValue(trim($value));
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }

    private static function processValue(string $value): string {
        if (strtolower($value) === 'true') return '1';
        if (strtolower($value) === 'false') return '0';
        if ($value === 'null') return '';
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
            return substr($value, 1, -1);
        }
        
        return $value;
    }
}

// .env.example
APP_NAME=QueryKicks
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=localhost
DB_DATABASE=query_kicks
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

// bootstrap.php
require_once __DIR__ . '/vendor/autoload.php';

EnvLoader::load(__DIR__ . '/.env');
Config::load(__DIR__ . '/config/app.php');

// Helper function
function env(string $key, $default = null) {
    return $_ENV[$key] ?? $default;
}
```

Want to see implementation of logging system next?