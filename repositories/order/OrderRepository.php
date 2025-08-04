<?php
require_once __DIR__ . '/../../models/order/Order.php';

class OrderRepository
{
    private mysqli $conn;
    private string $tableOrder = 'ds_order';
    private string $tableOrderItem = 'ds_order_item';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function findOrders(?int $customerId = null): array
    {
        $sql = "
            SELECT 
                o.*,
                oi.order_item_id,
                oi.outlet_id,
                oi.start_date,
                oi.end_date,
                oi.pos_no,
                oi.screen_id,
                outlet.outlet_name,
                screen.screen_name,
                campaign.campaign_name
            FROM {$this->tableOrder} o
            LEFT JOIN ds_order_item oi ON oi.order_id = o.order_id AND oi.is_deleted = 0
            LEFT JOIN ds_outlet outlet ON outlet.outlet_id = oi.outlet_id
            LEFT JOIN ds_screen screen ON screen.screen_id = oi.screen_id
            LEFT JOIN ds_campaign campaign ON campaign.campaign_id = o.campaign_id
            WHERE o.is_deleted = 0 AND o.customer_id = {$customerId}
        ";

        $result = $this->conn->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $orders = [];

        foreach ($rows as $row) {
            $orderId = $row['order_id'];

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_id'      => $row['order_id'],
                    'campaign_id'   => $row['campaign_id'],
                    'campaign_name' => $row['campaign_name'],
                    'order_name'    => $row['order_name'],
                    'created_by'    => $row['created_by'],
                    'customer_id'   => $row['customer_id'],
                    'duration'      => $row['duration'] ?? null, // ← ditambahkan
                    'created_at'    => $row['created_at'],
                    'order_items'   => [],
                ];
            }

            if ($row['order_item_id']) {
                $orders[$orderId]['order_items'][] = [
                    'order_item_id' => $row['order_item_id'],
                    'outlet_id'     => $row['outlet_id'],
                    'outlet_name'   => $row['outlet_name'] ?? null,
                    'start_date'    => $row['start_date'],
                    'end_date'      => $row['end_date'],
                    'pos_no'        => $row['pos_no'],
                    'screen_id'     => $row['screen_id'],
                    'screen_name'   => $row['screen_name'] ?? null,
                ];
            }
        }

        return array_values($orders);
    }


    public function findById(int $id): ?Screen
    {
        $sql = "SELECT * FROM {$this->tableOrder} WHERE order_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Screen::fromArray($result) : null;
    }

    public function createOrder(array $data): array
    {
        $sql = "INSERT INTO ds_order 
            (campaign_id, order_name, duration, created_by, customer_id) 
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'isisi',
            $data['campaign_id'],
            $data['order_name'],
            $data['duration'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
        ];

    }

    public function updateOrder(array $data): array
    {
        $sql = "UPDATE {$this->tableOrder}
                SET campaign_id = ?, order_name = ?, duration = ?
                WHERE order_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'isii',
            $data['campaign_id'],
            $data['order_name'],
            $data['duration'],
            $data['order_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function deleteOrder(int $id): array
    {
        $sql = "UPDATE {$this->tableOrder} SET is_deleted = 1 WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }

    ///////////////

    public function findOrderItem(?int $customerId = null, ?int $orderId = null): array
    {
        $sql = "SELECT 
                oi.*, 
                o.order_name,
                c.campaign_name,
                dso.outlet_name
            FROM ds_order_item oi
            LEFT JOIN ds_order o ON oi.order_id = o.order_id
            LEFT JOIN ds_campaign c ON o.campaign_id = c.campaign_id
            LEFT JOIN ds_outlet dso ON oi.outlet_id = dso.outlet_id
            WHERE oi.is_deleted = 0 AND oi.customer_id = ? AND oi.order_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $customerId, $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        $orderItems = [];
        while ($row = $result->fetch_assoc()) {
            $item = OrderItem::fromArray($row);
            $item->order_name = $row['order_name'] ?? null;
            $item->campaign_name = $row['campaign_name'] ?? null;
            $item->outlet_name = $row['outlet_name'] ?? null;
            $orderItems[] = $item;
        }

        return $orderItems;

    }


    public function findOrderItemById(int $id): ?Screen
    {
        $sql = "SELECT * FROM {$this->tableOrder} WHERE order_id = ? AND is_deleted = 0 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? Screen::fromArray($result) : null;
    }

    public function createOrderItem(array $data): array
    {
        $sql = "INSERT INTO ds_order_item
            (version_id, campaign_id, order_id, outlet_id, start_date, end_date, pos_no, screen_id, created_by, customer_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'iiiisssisi',
            $data['version_id'],
            $data['campaign_id'],
            $data['order_id'],
            $data['outlet_id'],
            $data['start_date'],
            $data['end_date'],
            $data['pos_no'],
            $data['screen_id'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
        ];

    }

    public function updateOrderItem(array $data): array
    {
        $sql = "UPDATE {$this->tableOrderItem}
                SET order_name = ?, duration = ?
                WHERE order_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sii',
            $data['order_name'],
            $data['duration'],
            $data['order_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }


    public function deleteOrderItem(int $id): array
    {
        $sql = "UPDATE {$this->tableOrderItem} SET is_deleted = 1 WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return ['status' => 'success'];
    }
}
