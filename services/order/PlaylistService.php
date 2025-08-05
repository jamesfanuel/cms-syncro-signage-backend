<?php
require_once __DIR__ . '/../../repositories/order/PlaylistRepository.php';

class PlaylistService
{
    private PlaylistRepository $repository;

    public function __construct(PlaylistRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find(?int $customerId = null, ?int $outletId = null): array
    {
        return $this->repository->find($customerId, $outletId);
    }

    public function findById(int $id): ?Playlist
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
