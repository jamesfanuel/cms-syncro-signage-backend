<?php
class LicenseService
{
    private LicenseRepository $repository;

    public function __construct(LicenseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate(array $data): License|string
    {
        return $this->repository->validate($data);
    }

    public function create(array $data): array
    {
        return $this->repository->create($data);
    }

    public function find(): array
    {
        return $this->repository->find();
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
