<?php
require_once __DIR__ . '/../../repositories/master/ClientRepository.php';

class ClientService
{
    private ClientRepository $repository;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find(?int $customerId = null): array
    {
        return $this->repository->find($customerId);
    }

    public function findById(int $id): ?Client
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
