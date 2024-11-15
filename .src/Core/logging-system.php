```php
// src/Core/Logging/LogLevel.php
class LogLevel {
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
}

// src/Core/Logging/Logger.php
class Logger {
    private string $path;
    private string $channel;

    public function __construct(string $channel = 'app') {
        $this->channel = $channel;
        $this->path = Config::get('logging.path', storage_path('logs'));
    }

    public function emergency(string $message, array $context = []): void {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string $message, array $context = []): void {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string $message, array $context = []): void {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string $message, array $context = []): void {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string $message, array $context = []): void {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string $message, array $context = []): void {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string $message, array $context = []): void {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string $message, array $context = []): void {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'channel' => $this->channel,
            'level' => $level,
            'message' => $this->interpolate($message, $context),
            'context' => $context
        ];

        $filename = sprintf(
            '%s/%s-%s.log',
            $this->path,
            $this->channel,
            date('Y-m-d')
        );

        file_put_contents(
            $filename,
            $this->formatLogEntry($logEntry) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    private function interpolate(string $message, array $context = []): string {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && !is_object($val)) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

    private function formatLogEntry(array $entry): string {
        return sprintf(
            '[%s] %s.%s: %s %s',
            $entry['timestamp'],
            $entry['channel'],
            $entry['level'],
            $entry['message'],
            json_encode($entry['context'])
        );
    }
}

// Usage
$logger = new Logger('api');

// Log examples
$logger->info('User logged in', ['user_id' => 123]);
$logger->error('Database connection failed', [
    'error' => 'Connection refused',
    'host' => 'localhost'
]);

// Example log entry:
// [2024-11-15 14:30:45] api.info: User logged in {"user_id":123}

// Helper function
function logger(string $channel = 'app'): Logger {
    static $loggers = [];
    
    if (!isset($loggers[$channel])) {
        $loggers[$channel] = new Logger($channel);
    }
    
    return $loggers[$channel];
}
```

Want to see session management implementation next?