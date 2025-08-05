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

    public function find(?int $customerId = null, ?int $outletId = null): array
    {
        $tanggalHariIni = date('Y-m-d');

        $sql = "SELECT p.*,
        o.outlet_name,
        s.screen_name,
        pv.version_name
        FROM {$this->table} p
        LEFT JOIN ds_outlet o ON p.outlet_id = o.outlet_id
        LEFT JOIN ds_screen s ON p.screen_id = s.screen_id
        LEFT JOIN ds_product_version pv ON p.version_id = pv.version_id
        WHERE p.customer_id = ? AND p.outlet_id = ? AND p.play_date = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $customerId, $outletId, $tanggalHariIni);
        $stmt->execute();
        $result = $stmt->get_result();

        $playlists = [];
        while ($row = $result->fetch_assoc()) {
            $playlists[] = Playlist::fromArray($row);
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
            DELETE FROM ds_playlist
            WHERE play_date = ?
            AND customer_id = ?
        ";

        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->bind_param('si', $tanggalHariIni, $customerId);
        $deleteStmt->execute();


        // Step 1: Ambil hasil recursive query
        $selectSql = "
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
            WHERE a.customer_id = ? AND b.start_date <= ? AND b.end_date >= ?
        ";

        $stmt = $this->conn->prepare($selectSql);
        $stmt->bind_param('iss', $customerId, $tanggalHariIni, $tanggalHariIni);
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
                order_duration,
                created_by,
                customer_id,
                play_date
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
                $row['duration'],
                $data['created_by'],
                $row['customer_id'],
                $tanggalHariIni
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
