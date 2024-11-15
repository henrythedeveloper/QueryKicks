```php
// src/Core/Notifications/NotificationInterface.php
interface NotificationInterface {
    public function via(): array;
    public function toMail(): Email;
    public function toDatabase(): array;
}

// src/Core/Notifications/NotificationManager.php
class NotificationManager {
    private array $channels = [];

    public function addChannel(string $name, NotificationChannel $channel): void {
        $this->channels[$name] = $channel;
    }

    public function send($notifiable, NotificationInterface $notification): void {
        foreach ($notification->via() as $channel) {
            if (isset($this->channels[$channel])) {
                $this->channels[$channel]->send($notifiable, $notification);
            }
        }
    }
}

// src/Core/Notifications/Channels/EmailChannel.php
class EmailChannel implements NotificationChannel {
    private MailerInterface $mailer;

    public function send($notifiable, NotificationInterface $notification): void {
        $email = $notification->toMail();
        $this->mailer->queue($email);
    }
}

// src/Core/Notifications/Channels/DatabaseChannel.php
class DatabaseChannel implements NotificationChannel {
    private PDO $db;

    public function send($notifiable, NotificationInterface $notification): void {
        $data = $notification->toDatabase();
        
        Database::query(
            "INSERT INTO notifications (user_id, type, data, read_at) 
             VALUES (:user_id, :type, :data, NULL)",
            [
                'user_id' => $notifiable->id,
                'type' => get_class($notification),
                'data' => json_encode($data)
            ]
        );
    }
}

// Example Notification
class OrderShippedNotification implements NotificationInterface {
    private Order $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function via(): array {
        return ['mail', 'database'];
    }

    public function toMail(): Email {
        return (new Email())
            ->subject('Your Order Has Been Shipped')
            ->body($this->render('emails.order-shipped', [
                'order' => $this->order
            ]));
    }

    public function toDatabase(): array {
        return [
            'order_id' => $this->order->id,
            'message' => "Order #{$this->order->id} has been shipped",
            'tracking_number' => $this->order->tracking_number
        ];
    }
}

// Usage
$notificationManager = new NotificationManager();
$notificationManager->addChannel('mail', new EmailChannel($mailer));
$notificationManager->addChannel('database', new DatabaseChannel());

// Send notification
$user = User::find(1);
$order = Order::find(123);
$notification = new OrderShippedNotification($order);
$notificationManager->send($user, $notification);
```

Want to see service container implementation next?