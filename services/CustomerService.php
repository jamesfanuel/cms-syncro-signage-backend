<?php
class CustomerService
{
    private CustomerRepository $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find(): array
    {
        return $this->repository->find();
    }

    public function findById(int $id): ?Customer
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
