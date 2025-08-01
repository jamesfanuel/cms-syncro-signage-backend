<?php
require_once __DIR__ . '/../models/Customer.php';

class CustomerRepository
{
    private mysqli $conn;
    private string $table = 'ds_customer';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function find(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0";
        $result = $this->conn->query($sql);

        $customers = [];
        while ($row = $result->fetch_assoc()) {
            $customers[] = Customer::fromArray($row);
        }

        return $customers;
    }

    public function findById(int $id): ?Customer
    {
        $sql = "SELECT * FROM {$this->table} WHERE customer_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Customer::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (customer_name, email, licence_date, created_by)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssss',
            $data['customer_name'],
            $data['email'],
            $data['licence_date'],
            $data['created_by']
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
                SET customer_name = ?, email = ?, licence_date = ?, created_by = ?
                WHERE customer_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssssi',
            $data['customer_name'],
            $data['email'],
            $data['licence_date'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE customer_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
