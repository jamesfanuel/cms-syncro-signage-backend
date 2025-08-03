<?php
require_once __DIR__ . '/../../models/master/Outlet.php';

class OutletRepository
{
    private mysqli $conn;
    private string $table = 'ds_outlet';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function find(?int $customerId = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0";
        
        if ($customerId !== null) {
            $sql .= " AND customer_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        $outlets = [];
        while ($row = $result->fetch_assoc()) {
            $outlets[] = Outlet::fromArray($row);
        }

        return $outlets;
    }


    public function findById(int $id): ?Outlet
    {
        $sql = "SELECT * FROM {$this->table} WHERE outlet_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Outlet::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (outlet_name, created_by, customer_id)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssi',
            $data['outlet_name'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
        ];
    }

    public function update(array $data): array
    {
        $sql = "UPDATE {$this->table}
                SET outlet_name = ?
                WHERE outlet_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'si',
            $data['outlet_name'],
            $data['outlet_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE outlet_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
