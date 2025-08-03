<?php
require_once __DIR__ . '/../../repositories/master/OutletRepository.php';

class OutletService
{
    private OutletRepository $repository;

    public function __construct(OutletRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find(?int $customerId = null): array
    {
        return $this->repository->find($customerId);
    }

    public function findById(int $id): ?Outlet
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): array
    {
        return $this->repository->create($data);
    }

    public function update(array $data): array
    {
        return $this->repository->update($data);
    }

    public function delete(int $id): array
    {
        return $this->repository->delete($id);
    }
}
