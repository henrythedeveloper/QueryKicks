```php
// src/Core/Config/Config.php
class Config {
    private static array $config = [];

    public static function load(string $path): void {
        if (!file_exists($path)) {
            throw new ConfigException("Config file not found: $path");
        }
        self::$config = require $path;
    }

    public static function get(string $key, $default = null) {
        return Arr::get(self::$config, $key, $default);
    }
}

// src/Core/Helpers/Arr.php
class Arr {
    public static function get(array $array, string $key, $default = null) {
        if (isset($array[$key])) {
            return $array[$key];
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
}

// config/app.php
return [
    'name' => 'QueryKicks',
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'database' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'localhost'),
        'database' => env('DB_DATABASE', 'query_kicks'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'mail' => [
        'driver' => env('MAIL_DRIVER', 'smtp'),
        'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
        'port' => env('MAIL_PORT', 2525),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
    ]
];

// Usage
Config::load(__DIR__ . '/config/app.php');
$dbConfig = Config::get('database');
$appName = Config::get('name');
```

Next: Setting up environment management with .env file?