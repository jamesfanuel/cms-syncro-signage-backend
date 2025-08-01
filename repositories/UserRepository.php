<?php
require_once __DIR__ . '/../models/User.php';

class UserRepository
{
    private mysqli $conn;
    private string $table = 'ds_user';


    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

     public function find(): array
    {
        $users = [];
        $table = User::TABLE_NAME;
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE is_deleted = 0");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = new User($row);
            }
        }

        return $users;
    }

    public function findByEmail(string $email): ?User
    {
        $table = User::TABLE_NAME;
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE email = ? AND is_deleted = 0 LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? new User($row) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $table = User::TABLE_NAME;
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE user_name = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? new User($row) : null;
    }

    public function create(User $data): array
    {
        $sql = "INSERT INTO {$this->table} (user_name, password, email, customer_id, expired_at, is_admin, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssisis',
            $data->user_name,
            $data->password,
            $data->email,
            $data->customer_id,
            $data->expired_at,
            $data->is_admin,
            $data->created_by
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id
        ];
    }

    public function update(array $data): array
    {
        $sql = "UPDATE {$this->table} 
                SET user_name = ?, email = ?, customer_id = ?, licence_date = ?, expired_at = ?, created_by = ?
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssisssi',
            $data['user_name'],
            $data['email'],
            $data['customer_id'],
            $data['licence_date'],
            $data['expired_at'],
            $data['created_by'],
            $data['user_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
