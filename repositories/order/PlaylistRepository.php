<?php
require_once __DIR__ . '/../../models/order/Playlist.php';

class PlaylistRepository
{
    private mysqli $conn;
    private string $table = 'ds_playlist';

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

        $playlists = [];
        while ($row = $result->fetch_assoc()) {
            $playlist = Playlist::fromArray($row);
            $playlist->client_name = $row['client_name'] ?? null;
            $playlists[] = $playlist;
        }

        return $playlists;

    }


    public function findById(int $id): ?Screen
    {
        $sql = "SELECT * FROM {$this->table} WHERE playlist_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Screen::fromArray($result) : null;
    }

    public function create(array $data): array
    {
        $tanggalHariIni = date('Y-m-d');
        $customerId = $data['customer_id'];

        // Step 0: DELETE berdasarkan hasil recursive
        $deleteSql = "
            WITH RECURSIVE date_range AS (
                SELECT
                    b.order_id,
                    b.outlet_id,
                    b.screen_id,
                    b.version_id,
                    b.start_date AS play_date,
                    b.start_date,
                    b.end_date
                FROM ds_order a
                JOIN ds_order_item b ON a.order_id = b.order_id
                JOIN ds_product_version c ON b.version_id = c.version_id

                UNION ALL

                SELECT
                    dr.order_id,
                    dr.outlet_id,
                    dr.screen_id,
                    dr.version_id,
                    DATE_ADD(dr.play_date, INTERVAL 1 DAY),
                    dr.start_date,
                    dr.end_date
                FROM date_range dr
                WHERE dr.play_date < dr.end_date
            )
            DELETE FROM ds_playlist p
            WHERE EXISTS (
                SELECT 1
                FROM date_range dr
                WHERE dr.play_date >= ?
                AND p.play_date = dr.play_date
                AND p.order_id = dr.order_id
                AND p.screen_id = dr.screen_id
                AND p.version_id = dr.version_id
                AND p.customer_id = ?
            )
        ";

        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->bind_param('si', $tanggalHariIni, $customerId);
        $deleteStmt->execute();


        // Step 1: Ambil hasil recursive query
        $selectSql = "
            WITH RECURSIVE date_range AS (
                SELECT
                    b.order_id,
                    a.order_name,
                    b.outlet_id,
                    o.outlet_name,
                    b.screen_id,
                    s.screen_name,
                    b.version_id,
                    a.duration,
                    c.file_path,
                    b.start_date AS play_date,
                    b.start_date,
                    b.end_date,
                    b.customer_id
                FROM ds_order a
                JOIN ds_order_item b ON a.order_id = b.order_id
                JOIN ds_product_version c ON b.version_id = c.version_id
                JOIN ds_outlet o ON b.outlet_id = o.outlet_id
                JOIN ds_screen s ON b.screen_id = s.screen_id

                UNION ALL

                SELECT
                    dr.order_id,
                    dr.order_name,
                    dr.outlet_id,
                    dr.outlet_name,
                    dr.screen_id,
                    dr.screen_name,
                    dr.version_id,
                    dr.duration,
                    dr.file_path,
                    DATE_ADD(dr.play_date, INTERVAL 1 DAY),
                    dr.start_date,
                    dr.end_date,
                    dr.customer_id
                FROM date_range dr
                WHERE dr.play_date < dr.end_date
            )
            SELECT 
                order_id,
                order_name,
                outlet_id,
                outlet_name,
                screen_id,
                screen_name,
                version_id,
                file_path,
                play_date,
                duration,
                customer_id
            FROM date_range 
            WHERE start_date <= ?
            AND end_date >= ?
        ";

        $stmt = $this->conn->prepare($selectSql);
        $stmt->bind_param('ss', $tanggalHariIni, $tanggalHariIni);
        $stmt->execute();
        $result = $stmt->get_result();

        $dataToInsert = [];
        while ($row = $result->fetch_assoc()) {
            $dataToInsert[] = $row;
        }

        // Step 2: Insert satu per satu
        $insertSql = "
            INSERT INTO ds_playlist (
                order_id,
                order_name,
                outlet_id,
                screen_id,
                version_id,
                file_path,
                play_date,
                order_duration,
                created_by,
                customer_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $insertStmt = $this->conn->prepare($insertSql);
        foreach ($dataToInsert as $row) {
            $insertStmt->bind_param(
                'isisssssis',
                $row['order_id'],
                $row['order_name'],
                $row['outlet_id'],
                $row['screen_id'],
                $row['version_id'],
                $row['file_path'],
                $row['play_date'],
                $row['duration'],
                $data['created_by'],
                $row['customer_id']
            );
            $insertStmt->execute();
        }

        return [
            'status' => 'success',
            'inserted_count' => count($dataToInsert),
            'data' => $dataToInsert
        ];
    }

    public function update(array $data): array
    {
        $sql = "UPDATE {$this->table}
                SET playlist_name = ?, client_id = ?,  start_date = ?, end_date = ?, created_by = ?, customer_id = ?
                WHERE playlist_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sisssii',
            $data['playlist_name'],
            $data['client_id'],
            $data['start_date'],
            $data['end_date'],
            $data['created_by'],
            $data['customer_id'],
            $data['playlist_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function delete(int $id): array
    {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE playlist_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
