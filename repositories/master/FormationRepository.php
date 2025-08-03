<?php
require_once __DIR__ . '/../../models/master/Formation.php';

class FormationRepository
{
    private mysqli $conn;
    private string $table = 'ds_screen';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function find(?int $customerId = null): array
    {
        $sql = "SELECT s.*, o.outlet_name 
        FROM {$this->table} s
        LEFT JOIN ds_outlet o ON s.outlet_id = o.outlet_id
        WHERE s.is_deleted = 0";

        if ($customerId !== null) {
            $sql .= " AND s.customer_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        $screens = [];
        while ($row = $result->fetch_assoc()) {
            $screen = Formation::fromArray($row);
            $screen->outlet_name = $row['outlet_name'] ?? null;
            $screens[] = $screen;
        }

        return $screens;

    }


    public function findById(int $id): ?Screen
    {
        $sql = "SELECT * FROM {$this->table} WHERE screen_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Screen::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (outlet_id, screen_name, screen_description, screen_function, created_by, customer_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'issssi',
            $data['outlet_id'],
            $data['screen_name'],
            $data['screen_description'],
            $data['screen_function'],
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
                SET outlet_id = ?, screen_name = ?, screen_description = ?, screen_function = ?, created_by = ?, customer_id = ?
                WHERE screen_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'issssii',
            $data['outlet_id'],
            $data['screen_name'],
            $data['screen_description'],
            $data['screen_function'],
            $data['created_by'],
            $data['customer_id'],
            $data['screen_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE screen_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
