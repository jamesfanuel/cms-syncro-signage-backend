<?php

class ProductController
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    // Category
    public function findCategories(?int $customerId = null): void
    {
        $categories = $this->service->findCategories($customerId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $categories]);
    }

    public function createCategory()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['category_name'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'category_name wajib diisi']);
            return;
        }

        $result = $this->service->createCategory($payload);
        echo json_encode($result);
    }

    public function updateCategory(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['category_id'] = $id;
        $result = $this->service->updateCategory($payload);
        echo json_encode($result);
    }

    public function deleteCategory(int $id)
    {
        $result = $this->service->deleteCategory($id);
        echo json_encode($result);
    }

    // Product
    public function findProducts(?int $customerId = null): void
    {
        $products = $this->service->findProducts($customerId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $products]);
    }

    public function createProduct()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['product_name'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'product_name wajib diisi']);
            return;
        }

        $result = $this->service->createProduct($payload);
        echo json_encode($result);
    }

    public function updateProduct(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['product_id'] = $id;
        $result = $this->service->updateProduct($payload);
        echo json_encode($result);
    }

    public function deleteProduct(int $id)
    {
        $result = $this->service->deleteProduct($id);
        echo json_encode($result);
    }

    // Product Version
    public function findVersions(?int $customerId = null): void
    {
        $versions = $this->service->findVersions($customerId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $versions]);
    }

    public function createVersion()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['product_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'product_id wajib diisi']);
            return;
        }

        $result = $this->service->createVersion($payload);
        echo json_encode($result);
    }

    public function updateVersion(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['version_id'] = $id;
        $result = $this->service->updateVersion($payload);
        echo json_encode($result);
    }

    public function deleteVersion(int $id)
    {
        $result = $this->service->deleteVersion($id);
        echo json_encode($result);
    }

    public function uploadVersion(int $versionId)
    {
        $result = $this->service->uploadVersion($versionId);
        echo json_encode(['status' => 'success', 'data' => $result]);
    }
}
