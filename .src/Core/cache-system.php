```php
// src/Core/Cache/CacheInterface.php
interface CacheInterface {
    public function get(string $key, $default = null);
    public function set(string $key, $value, ?int $ttl = null): bool;
    public function delete(string $key): bool;
    public function clear(): bool;
    public function has(string $key): bool;
}

// src/Core/Cache/FileCache.php
class FileCache implements CacheInterface {
    private string $path;

    public function __construct(string $path) {
        $this->path = rtrim($path, '/');
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public function get(string $key, $default = null) {
        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return $default;
        }

        $data = unserialize(file_get_contents($filename));
        if ($data['expiry'] && time() > $data['expiry']) {
            unlink($filename);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, $value, ?int $ttl = null): bool {
        $data = [
            'value' => $value,
            'expiry' => $ttl ? time() + $ttl : null
        ];

        return file_put_contents(
            $this->getFilename($key),
            serialize($data),
            LOCK_EX
        ) !== false;
    }

    private function getFilename(string $key): string {
        return $this->path . '/' . md5($key) . '.cache';
    }
}

// src/Core/Cache/RedisCache.php
class RedisCache implements CacheInterface {
    private Redis $redis;

    public function __construct(array $config) {
        $this->redis = new Redis();
        $this->redis->connect(
            $config['host'] ?? 'localhost',
            $config['port'] ?? 6379
        );

        if (isset($config['password'])) {
            $this->redis->auth($config['password']);
        }
    }

    public function get(string $key, $default = null) {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : $default;
    }

    public function set(string $key, $value, ?int $ttl = null): bool {
        return $ttl 
            ? $this->redis->setex($key, $ttl, serialize($value))
            : $this->redis->set($key, serialize($value));
    }
}

// src/Core/Cache/Cache.php
class Cache {
    private static ?CacheInterface $driver = null;

    public static function init(array $config): void {
        $driver = $config['driver'] ?? 'file';
        
        self::$driver = match($driver) {
            'redis' => new RedisCache($config),
            'file' => new FileCache($config['path']),
            default => throw new CacheException("Unknown cache driver: $driver")
        };
    }

    public static function remember(string $key, int $ttl, callable $callback) {
        if (self::has($key)) {
            return self::get($key);
        }

        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }

    public static function tags(array $tags): TaggedCache {
        return new TaggedCache(self::$driver, $tags);
    }

    public static function __callStatic(string $method, array $arguments) {
        return self::$driver->$method(...$arguments);
    }
}

// Usage Example
Cache::init([
    'driver' => 'redis',
    'host' => 'localhost',
    'port' => 6379
]);

// Basic usage
Cache::set('key', 'value', 3600);
$value = Cache::get('key');

// Remember pattern
$products = Cache::remember('products', 3600, function() {
    return ProductModel::all();
});

// Tagged cache
Cache::tags(['products'])->set('featured', $featuredProducts);
Cache::tags(['products'])->flush();
```

Want to see queue system implementation next?