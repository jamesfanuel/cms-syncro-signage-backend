<?php
class OrderController
{
    private OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    public function findOrders(?int $customerId = null): void
    {
        $orderItems = $this->service->findOrders($customerId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $orderItems]);
    }


    public function findOrdersById(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $order = $this->service->findOrdersById($id);
        echo json_encode(['status' => 'success', 'data' => $order]);
    }

    public function createOrder()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['campaign_id']) || !isset($payload['customer_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid atau campaign_id kosong atau customer_id kosong']);
            return;
        }

        $result = $this->service->createOrder($payload);
        echo json_encode($result);
    }

    public function updateOrder(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['order_id'] = $id;
        $result = $this->service->updateOrder($payload);
        echo json_encode($result);
    }

    public function deleteOrder(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $result = $this->service->deleteOrder($id);
        echo json_encode($result);
    }

    //// Order Item

    public function findOrderItem(?int $customerId = null, ?int $orderId = null): void
    {
        $orderItems = $this->service->findOrderItem($customerId, $orderId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $orderItems]);
    }


    public function findOrderItemById(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $customer = $this->service->findOrderItemById($id);
        echo json_encode(['status' => 'success', 'data' => $customer]);
    }

    public function createOrderItem()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['customer_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid atau customer_id kosong']);
            return;
        }

        $result = $this->service->createOrderItem($payload);
        echo json_encode($result);
    }

    public function updateOrderItem(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['outlet_id'] = $id;
        $result = $this->service->updateOrderItem($payload);
        echo json_encode($result);
    }

    public function deleteOrderItem(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $result = $this->service->deleteOrderItem($id);
        echo json_encode($result);
    }
}
