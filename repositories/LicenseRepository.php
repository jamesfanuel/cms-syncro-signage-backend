<?php
require_once __DIR__ . '/../models/License.php';

class LicenseRepository
{
    private mysqli $conn;
    private string $table = 'ds_license';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function find(): array
    {
        $sql = "SELECT l.*, 
               c.customer_name, 
               o.outlet_name, 
               s.screen_name
        FROM {$this->table} l
        INNER JOIN ds_customer c ON l.customer_id = c.customer_id
        INNER JOIN ds_outlet o ON l.outlet_id = o.outlet_id
        INNER JOIN ds_screen s ON l.screen_id = s.screen_id
        WHERE l.is_deleted = 0";

        $result = $this->conn->query($sql);

        $licenses = [];
        while ($row = $result->fetch_assoc()) {
            $licenses[] = License::fromArray($row);
        }

        return $licenses;
    }

    public function findById(int $id): ?License
    {
        $sql = "SELECT * FROM {$this->table} WHERE license_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? License::fromArray($result) : null;
    }

    public function validate(array $data): License|string|null
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE license_code = ? AND is_deleted = 0 
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $data['license_code']);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return null; // Tidak ditemukan
        }

        $today = date('Y-m-d');
        $expiryDate = $result['expired_at'];

        if ($expiryDate < $today) {
            return 'EXPIRED';
        }

        return License::fromArray($result);
    }

    public function create(array $data): array
    {
        $licenceCode = $this->generateLicenseCode();
        $sql = "INSERT INTO {$this->table} 
            (license_code, customer_id, outlet_id, screen_id, expired_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'siiisi',
            $licenceCode,
            $data['customer_id'],
            $data['outlet_id'],
            $data['screen_id'],
            $data['expired_at'],
            $data['created_by']
        );

        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
            'license-code' => $licenceCode
        ];

    }

    function generateLicenseCode(int $segments = 4, int $lengthPerSegment = 4): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = [];

        for ($i = 0; $i < $segments; $i++) {
            $segment = '';
            for ($j = 0; $j < $lengthPerSegment; $j++) {
                $segment .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $code[] = $segment;
        }

        return implode('-', $code);
    }

    public function update(array $data): array
    {
        $sql = "UPDATE {$this->table} 
                SET customer_id = ?, outlet_id = ?, screen_id = ?, expired_at = ?
                WHERE license_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'iiisi',
            $data['customer_id'],
            $data['outlet_id'],
            $data['screen_id'],
            $data['expired_at'],
            $data['license_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE license_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
