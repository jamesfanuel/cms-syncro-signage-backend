<?php
require_once __DIR__ . '/../../models/master/Client.php';

class ClientRepository
{
    private mysqli $conn;
    private string $table = 'ds_client';

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

        $clients = [];
        while ($row = $result->fetch_assoc()) {
            $clients[] = Client::fromArray($row);
        }

        return $clients;
    }


    public function findById(int $id): ?Client
    {
        $sql = "SELECT * FROM {$this->table} WHERE client_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Client::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $lastCode = $this->getLastClientCode();
        $nextCode = $this->generateNextCode($lastCode);

        $sql = "INSERT INTO {$this->table} (client_code, client_name, created_by, customer_id)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssi',
            $nextCode,
            $data['client_name'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
            'client_code' => $nextCode
        ];
    }

    private function getLastClientCode(): ?string
    {
        $sql = "SELECT client_code FROM {$this->table} 
                WHERE client_code LIKE 'C%' 
                ORDER BY client_id DESC 
                LIMIT 1";

        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            return $row['client_code'];
        }
        return null;
    }

    private function generateNextCode(?string $lastCode): string
    {
        if (!$lastCode) {
            return 'C001';
        }

        $num = (int)substr($lastCode, 1); // ambil angka dari C001 → 1
        $nextNum = $num + 1;

        return 'C' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }


    public function update(array $data): array
    {
        $sql = "UPDATE {$this->table}
                SET client_code = ?, client_name = ?, created_by = ?, customer_id = ?
                WHERE client_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssii',
            $data['client_code'],
            $data['client_name'],
            $data['created_by'],
            $data['customer_id'],
            $data['client_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE client_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
