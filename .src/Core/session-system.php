```php
// src/Core/Session/SessionManager.php
class SessionManager {
    private array $config;

    public function __construct(array $config = []) {
        $this->config = array_merge([
            'name' => 'QUERYKICKS_SESSION',
            'lifetime' => 7200,
            'path' => '/',
            'domain' => null,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ], $config);
    }

    public function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->config['name']);
            
            session_set_cookie_params([
                'lifetime' => $this->config['lifetime'],
                'path' => $this->config['path'],
                'domain' => $this->config['domain'],
                'secure' => $this->config['secure'],
                'httponly' => $this->config['httponly'],
                'samesite' => $this->config['samesite']
            ]);

            session_start();
            
            if (!$this->isValid()) {
                $this->regenerate();
            }
        }
    }

    public function regenerate(): bool {
        return session_regenerate_id(true);
    }

    public function destroy(): bool {
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        return session_destroy();
    }

    private function isValid(): bool {
        if (!isset($_SESSION['_last_activity'])) {
            $_SESSION['_last_activity'] = time();
            return true;
        }

        if (time() - $_SESSION['_last_activity'] > $this->config['lifetime']) {
            return false;
        }

        $_SESSION['_last_activity'] = time();
        return true;
    }
}

// src/Core/Session/SessionAuth.php
class SessionAuth {
    private SessionManager $session;

    public function __construct(SessionManager $session) {
        $this->session = $session;
    }

    public function login(array $user): void {
        $this->session->regenerate();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
    }

    public function logout(): void {
        $this->session->destroy();
    }

    public function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public function user(): ?array {
        if (!$this->check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }
}

// Usage in index.php
$session = new SessionManager(Config::get('session'));
$session->start();

$auth = new SessionAuth($session);

// Usage in AuthController
class AuthController extends BaseController {
    private UserService $userService;
    private SessionAuth $auth;

    public function login(): void {
        try {
            $user = $this->userService->authenticate(
                $_POST['email'],
                $_POST['password']
            );
            
            $this->auth->login($user);
            $this->redirect('/dashboard');
        } catch (AuthException $e) {
            $this->view('auth/login', ['error' => $e->getMessage()]);
        }
    }

    public function logout(): void {
        $this->auth->logout();
        $this->redirect('/login');
    }
}
```

Want to see cache system implementation next?