<?php

require_once __DIR__ . '/../../repositories/master/ProductRepository.php';

class ProductService
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    // === CATEGORY ===
    public function findCategories(?int $customerId = null): array
    {
        return $this->repository->findCategories($customerId);
    }

    public function findCategoryById(int $id): ?array
    {
        return $this->repository->findCategoryById($id);
    }

    public function createCategory(array $data): array
    {
        return $this->repository->createCategory($data);
    }

    public function updateCategory(array $data): array
    {
        return $this->repository->updateCategory($data);
    }

    public function deleteCategory(int $id): array
    {
        return $this->repository->deleteCategory($id);
    }

    // === PRODUCT ===
    public function findProducts(?int $customerId = null): array
    {
        return $this->repository->findProducts($customerId);
    }

    public function findProductById(int $id): ?array
    {
        return $this->repository->findProductById($id);
    }

    public function createProduct(array $data): array
    {
        return $this->repository->createProduct($data);
    }

    public function updateProduct(array $data): array
    {
        return $this->repository->updateProduct($data);
    }

    public function deleteProduct(int $id): array
    {
        return $this->repository->deleteProduct($id);
    }

    // === VERSION ===
    public function findVersions(int $customerId): array
    {
        return $this->repository->findVersions($customerId);
    }

    public function findVersionById(int $id): ?array
    {
        return $this->repository->findVersionById($id);
    }

    public function createVersion(array $data): array
    {
        return $this->repository->createVersion($data);
    }

    public function updateVersion(array $data): array
    {
        return $this->repository->updateVersion($data);
    }

    public function deleteVersion(int $id): array
    {
        return $this->repository->deleteVersion($id);
    }

    public function uploadVersion(int $versionId): array
    {
        $logFile = __DIR__ . '/../../logs/upload.log';
        $chunkDir = __DIR__ . "/../../uploads/chunks/{$versionId}/";
        $statusFile = __DIR__ . "/../../uploads/status_{$versionId}.json";

        if (!isset($_FILES['file']) || !isset($_POST['chunk_index']) || !isset($_POST['total_chunks']) || !isset($_POST['file_name'])) {
            return ['status' => 'error', 'message' => 'Invalid upload data'];
        }

        $chunkIndex = (int)$_POST['chunk_index'];
        $totalChunks = (int)$_POST['total_chunks'];
        $originName = basename($_POST['file_name']);
        $chunkFile = $_FILES['file'];

        // Buat folder untuk chunk
        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        $chunkPath = "{$chunkDir}/chunk_{$chunkIndex}";
        if (!move_uploaded_file($chunkFile['tmp_name'], $chunkPath)) {
            return ['status' => 'error', 'message' => "Failed to save chunk $chunkIndex"];
        }

        // Logging status
        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Received chunk {$chunkIndex}/{$totalChunks} for {$originName}\n", FILE_APPEND);

        // Cek apakah semua chunk sudah masuk
        $uploadedChunks = glob("{$chunkDir}/chunk_*");
        if (count($uploadedChunks) < $totalChunks) {
            file_put_contents($statusFile, json_encode([
                'status' => 'uploading',
                'progress' => round(count($uploadedChunks) / $totalChunks * 100),
                'file_name' => $originName,
                'timestamp' => time(),
            ]));

            return ['status' => 'uploading', 'progress' => round(count($uploadedChunks) / $totalChunks * 100)];
        }

        // Gabungkan semua chunk
        $ext = pathinfo($originName, PATHINFO_EXTENSION);
        $newName = uniqid('video_') . '.' . $ext;
        $finalPath = __DIR__ . "/../../uploads/{$newName}";
        $output = fopen($finalPath, 'ab');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = "{$chunkDir}/chunk_{$i}";
            $input = fopen($chunkFile, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
            unlink($chunkFile); // Hapus chunk setelah digabung
        }

        fclose($output);
        rmdir($chunkDir); // Bersihkan folder

        // Update DB
        $fileSize = filesize($finalPath);
        $result = $this->repository->uploadVersionFile($versionId, [
            'file_path' => '/uploads/' . $newName,
            'origin_file_name' => $originName,
            'file_name' => $newName,
            'file_size' => $fileSize,
        ]);

        file_put_contents($statusFile, json_encode([
            'status' => 'completed',
            'file_name' => $originName,
            'saved_as' => $newName,
            'file_size' => $fileSize,
            'timestamp' => time(),
        ]));

        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " File {$originName} uploaded as {$newName}\n", FILE_APPEND);

        return $result;
    }
}
