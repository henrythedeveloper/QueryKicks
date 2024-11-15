```php
// src/Core/Queue/QueueInterface.php
interface QueueInterface {
    public function push(string $job, array $data = []): string;
    public function pop(string $queue = 'default'): ?array;
    public function delete(string $id): bool;
    public function release(string $id, int $delay = 0): bool;
}

// src/Core/Queue/DatabaseQueue.php
class DatabaseQueue implements QueueInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->createJobsTable();
    }

    public function push(string $job, array $data = []): string {
        $id = uniqid('job_');
        
        $sql = "INSERT INTO jobs (id, queue, job, data, attempts, created_at) 
                VALUES (:id, :queue, :job, :data, 0, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'queue' => 'default',
            'job' => $job,
            'data' => json_encode($data)
        ]);

        return $id;
    }

    private function createJobsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS jobs (
            id VARCHAR(32) PRIMARY KEY,
            queue VARCHAR(255) NOT NULL,
            job VARCHAR(255) NOT NULL,
            data TEXT,
            attempts INT DEFAULT 0,
            reserved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }
}

// src/Core/Queue/Job.php
abstract class Job {
    protected array $data;

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    abstract public function handle(): void;

    public function failed(Throwable $e): void {
        logger()->error('Job failed', [
            'job' => static::class,
            'error' => $e->getMessage(),
            'data' => $this->data
        ]);
    }
}

// src/Core/Queue/Worker.php
class Worker {
    private QueueInterface $queue;
    private int $sleep = 3;
    private int $maxTries = 3;

    public function work(string $queue = 'default'): void {
        while (true) {
            if ($job = $this->queue->pop($queue)) {
                try {
                    $this->process($job);
                } catch (Exception $e) {
                    $this->handleFailedJob($job, $e);
                }
            }
            sleep($this->sleep);
        }
    }

    private function process(array $job): void {
        $instance = new $job['job']($job['data']);
        $instance->handle();
        $this->queue->delete($job['id']);
    }
}

// Example Job Implementation
class SendEmailJob extends Job {
    public function handle(): void {
        $mailer = new Mailer();
        $mailer->send(
            $this->data['to'],
            $this->data['subject'],
            $this->data['body']
        );
    }
}

// Example Usage
$queue = new DatabaseQueue(Database::getInstance());

// Dispatch a job
$queue->push(SendEmailJob::class, [
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'body' => 'Welcome to our platform!'
]);

// Process jobs (in a separate process/worker)
$worker = new Worker($queue);
$worker->work();
```

Want to see the email system implementation next?