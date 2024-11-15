class UserModel extends BaseModel {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return Database::query(
            "SELECT * FROM {$this->table} WHERE email = :email",
            ['email' => $email]
        )->fetch() ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}