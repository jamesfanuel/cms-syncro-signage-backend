<?php
require_once __DIR__ . '/../../models/order/Campaign.php';

class CampaignRepository
{
    private mysqli $conn;
    private string $table = 'ds_campaign';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function find(?int $customerId = null): array
    {
        $sql = "SELECT s.*, c.client_name 
        FROM {$this->table} s
        LEFT JOIN ds_client c ON s.client_id = c.client_id
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

        $campaigns = [];
        while ($row = $result->fetch_assoc()) {
            $campaign = Campaign::fromArray($row);
            $campaign->client_name = $row['client_name'] ?? null;
            $campaigns[] = $campaign;
        }

        return $campaigns;

    }


    public function findById(int $id): ?Screen
    {
        $sql = "SELECT * FROM {$this->table} WHERE campaign_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Screen::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (campaign_name, client_id, start_date, end_date, created_by, customer_id)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sisssi',
            $data['campaign_name'],
            $data['client_id'],
            $data['start_date'],
            $data['end_date'],
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
                SET campaign_name = ?, client_id = ?,  start_date = ?, end_date = ?, created_by = ?, customer_id = ?
                WHERE campaign_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sisssii',
            $data['campaign_name'],
            $data['client_id'],
            $data['start_date'],
            $data['end_date'],
            $data['created_by'],
            $data['customer_id'],
            $data['campaign_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE campaign_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
