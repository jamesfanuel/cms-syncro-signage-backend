<?php
require_once __DIR__ . '/../../repositories/order/OrderRepository.php';

class OrderService
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOrders(?int $customerId = null): array
    {
        return $this->repository->findOrders($customerId);
    }

    public function findOrdersById(int $id): ?Order
    {
        return $this->repository->findOrdersById($id);
    }

    public function createOrder(array $data): array
    {
        return $this->repository->createOrder($data);
    }

    public function updateOrder(array $data): array
    {
        return $this->repository->updateOrder($data);
    }

    public function deleteOrder(int $id): array
    {
        return $this->repository->deleteOrder($id);
    }

    ///////

    public function findOrderItem(?int $customerId = null, ?int $orderId = null): array
    {
        return $this->repository->findOrderItem($customerId, $orderId);
    }

    public function findOrderItemById(int $id): ?Order
    {
        return $this->repository->findOrderItemById($id);
    }

    public function createOrderItem(array $data): array
    {
        return $this->repository->createOrderItem($data);
    }

    public function updateOrderItem(array $data): array
    {
        return $this->repository->updateOrderItem($data);
    }

    public function deleteOrderItem(int $id): array
    {
        return $this->repository->deleteOrderItem($id);
    }
}
