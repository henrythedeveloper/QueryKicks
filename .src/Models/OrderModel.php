// Example OrderModel.php:
class OrderModel extends BaseModel {
    protected string $table = 'orders';

    public function getUserOrders(int $userId): array {
        return Database::query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->fetchAll();
    }
}