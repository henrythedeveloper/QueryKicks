```php
// src/Core/Mail/MailerInterface.php
interface MailerInterface {
    public function send(Email $email): bool;
    public function queue(Email $email): string;
}

// src/Core/Mail/Email.php
class Email {
    private array $to = [];
    private array $cc = [];
    private array $bcc = [];
    private string $subject = '';
    private string $body = '';
    private array $attachments = [];
    private string $from = '';
    private string $replyTo = '';

    public function to(string|array $address): self {
        $this->to = is_array($address) ? $address : [$address];
        return $this;
    }

    public function subject(string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $body): self {
        $this->body = $body;
        return $this;
    }

    public function attach(string $path, string $name = null): self {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?? basename($path)
        ];
        return $this;
    }
}

// src/Core/Mail/SmtpMailer.php
class SmtpMailer implements MailerInterface {
    private array $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function send(Email $email): bool {
        try {
            $mailer = new PHPMailer(true);
            
            // Server settings
            $mailer->isSMTP();
            $mailer->Host = $this->config['host'];
            $mailer->Port = $this->config['port'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->config['username'];
            $mailer->Password = $this->config['password'];
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            
            // Content
            $mailer->isHTML(true);
            $mailer->Subject = $email->getSubject();
            $mailer->Body = $email->getBody();
            
            foreach ($email->getTo() as $address) {
                $mailer->addAddress($address);
            }
            
            foreach ($email->getAttachments() as $attachment) {
                $mailer->addAttachment(
                    $attachment['path'],
                    $attachment['name']
                );
            }
            
            return $mailer->send();
        } catch (Exception $e) {
            logger()->error('Email sending failed', [
                'error' => $e->getMessage(),
                'to' => $email->getTo()
            ]);
            return false;
        }
    }

    public function queue(Email $email): string {
        return Queue::push(SendEmailJob::class, [
            'email' => serialize($email)
        ]);
    }
}

// src/Core/Mail/Templates/EmailTemplate.php
abstract class EmailTemplate {
    protected array $data = [];

    public function __construct(array $data = []) {
        $this->data = $data;
    }

    abstract public function subject(): string;
    abstract public function body(): string;

    protected function render(string $template, array $data = []): string {
        ob_start();
        extract(array_merge($this->data, $data));
        require "views/emails/$template.php";
        return ob_get_clean();
    }
}

// Example Welcome Email Template
class WelcomeEmail extends EmailTemplate {
    public function subject(): string {
        return "Welcome to " . Config::get('app.name');
    }

    public function body(): string {
        return $this->render('welcome', [
            'name' => $this->data['name']
        ]);
    }
}

// Usage
$mailer = new SmtpMailer(Config::get('mail'));

// Send immediate email
$email = (new Email())
    ->to('user@example.com')
    ->subject('Welcome')
    ->body('Welcome to our platform!')
    ->attach('path/to/file.pdf');

$mailer->send($email);

// Send template email
$welcomeEmail = new WelcomeEmail(['name' => 'John']);
$email = (new Email())
    ->to('john@example.com')
    ->subject($welcomeEmail->subject())
    ->body($welcomeEmail->body());

// Queue email for later sending
$mailer->queue($email);
```

Want to see notification system implementation next?