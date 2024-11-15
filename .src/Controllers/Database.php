```php
/**
 * Database connection management class using PDO
 * Implements Singleton pattern for single database connection
 */
class Database {
    /** @var PDO|null Stores the database connection */
    private static ?PDO $instance = null;
    
    /** @var array Stores database configuration */
    private static array $config = [];

    /**
     * Private constructor to prevent direct object creation
     * Enforces singleton pattern
     */
    private function __construct() {}

    /**
     * Configure database connection parameters
     *
     * @param array $config Database configuration options
     */
    public static function configure(array $config): void {
        self::$config = $config;
    }

    /**
     * Get database connection instance
     * Creates new connection if none exists
     *
     * @return PDO
     * @throws DatabaseException If connection fails
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=utf8mb4",
                    self::$config['host'] ?? 'localhost',
                    self::$config['database'] ?? 'query_kicks'
                );

                self::$instance = new PDO(
                    $dsn,
                    self::$config['username'] ?? 'root',
                    self::$config['password'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (PDOException $e) {
                throw new DatabaseException(
                    "Connection failed: " . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }
        return self::$instance;
    }

    /**
     * Execute a query with parameters
     *
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return PDOStatement
     */
    public static function query(string $query, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Begin a transaction
     */
    public static function beginTransaction(): void {
        self::getInstance()->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public static function commit(): void {
        self::getInstance()->commit();
    }

    /**
     * Rollback a transaction
     */
    public static function rollback(): void {
        self::getInstance()->rollBack();
    }
}

/**
 * Custom exception for database errors
 */
class DatabaseException extends Exception {
    // Custom exception methods can be added here
}

// Usage example:
try {
    // Configure database
    Database::configure([
        'host' => 'localhost',
        'database' => 'query_kicks',
        'username' => 'root',
        'password' => ''
    ]);

    // Example query with parameters
    $products = Database::query(
        "SELECT * FROM products WHERE price > :price",
        ['price' => 100]
    )->fetchAll();

    // Transaction example
    Database::beginTransaction();
    try {
        Database::query("INSERT INTO products (name, price) VALUES (:name, :price)", [
            'name' => 'New Product',
            'price' => 199.99
        ]);
        Database::commit();
    } catch (Exception $e) {
        Database::rollback();
        throw $e;
    }
} catch (DatabaseException $e) {
    // Handle database errors
    error_log($e->getMessage());
}
```

Key features:

Singleton pattern ensures single connection
Prepared statements prevent SQL injection
Transaction support for data integrity
Error handling with custom exception
Configuration management
UTF-8 support

Need any specific part explained?