<?php
require_once __DIR__ . '/../../models/master/Product.php';

class ProductRepository
{
    private mysqli $conn;    
    private $tableCategory = 'ds_product_category';
    private $tableProduct = 'ds_product_item';
    private $tableVersion = 'ds_product_version';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ===============================
    // Product Category CRUD
    // ===============================

    public function findCategories(?int $customerId = null): array
    {
        $sql = "SELECT * FROM {$this->tableCategory} WHERE is_deleted = 0";
        
        if ($customerId !== null) {
            $sql .= " AND customer_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = ProductCategory::fromArray($row);
        }

        return $categories;
    }

    public function findCategoryById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->tableCategory} WHERE category_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createCategory($data)
    {
        $sql = "INSERT INTO {$this->tableCategory} (category_name, created_by, customer_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $data['category_name'], $data['created_by'], $data['customer_id']);
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
            'category_name' => $data['category_name']
        ];
    }

    public function updateCategory($data)
    {
        $sql = "UPDATE {$this->tableCategory} SET category_name = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $data['category_name'], $data['category_id']);
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function deleteCategory($id)
    {
        $sql = "UPDATE {$this->tableCategory} SET is_deleted = 1 WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return ['status' => 'success'];
    }

    // ===============================
    // Product CRUD
    // ===============================

    public function findProducts(?int $customerId = null): array
    {
        $sql = "
        SELECT 
            p.*, 
            c.client_name,
            cat.category_name,
            v.version_id,
            v.version_name,
            v.resolution,
            v.file_name,
            v.file_path,
            v.origin_file_name,
            v.file_size,
            v.duration,
            v.md5,
            v.thumbnail
        FROM {$this->tableProduct} p
        LEFT JOIN ds_client c ON c.client_id = p.client_id AND c.is_deleted = 0
        LEFT JOIN ds_product_category cat ON cat.category_id = p.category_id AND cat.is_deleted = 0
        LEFT JOIN ds_product_version v ON v.product_id = p.product_id AND v.is_deleted = 0
        WHERE p.is_deleted = 0 AND p.customer_id = {$customerId}
        ";

        $result = $this->conn->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $products = [];

        foreach ($rows as $row) {
            $productId = $row['product_id'];

            if (!isset($products[$productId])) {
                $products[$productId] = [
                    'product_id' => $row['product_id'],
                    'product_code' => $row['product_code'],
                    'product_name' => $row['product_name'],
                    'created_by' => $row['created_by'],
                    'customer_id' => $row['customer_id'],
                    'client_id' => $row['client_id'],
                    'client_name' => $row['client_name'],
                    'category_id' => $row['category_id'],
                    'end_date' => $row['end_date'],
                    'category_name' => $row['category_name'],
                    'created_at' => $row['created_at'],
                    'product_versions' => [],
                ];
            }

            if ($row['version_id']) {
                $products[$productId]['product_versions'][] = [
                    'version_id' => $row['version_id'],
                    'version_name' => $row['version_name'],
                    'resolution' => $row['resolution'],
                    'file_name' => $row['file_name'],
                    'file_path' => $row['file_path'],
                    'origin_file_name' => $row['origin_file_name'],
                    'file_size' => $row['file_size'],
                    'duration' => $row['duration'],
                    'md5' => $row['md5'],
                    'thumbnail' => $row['thumbnail'],
                ];
            }
        }

        return array_values($products);
    }

    public function findProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->tableProduct} WHERE product_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createProduct($data)
    {
        $lastCode = $this->getLastProductCode();
        $nextCode = $this->generateNextCode($lastCode);

        $sql = "INSERT INTO {$this->tableProduct} (product_code, product_name, client_id, category_id, end_date, customer_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiisis", $nextCode, $data['product_name'], $data['client_id'], $data['category_id'], $data['end_date'], $data['customer_id'], $data['created_by']);
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
            'product_code' => $nextCode
        ];
    }

    private function getLastProductCode(): ?string
    {
        $sql = "SELECT product_code FROM {$this->tableProduct} 
                WHERE product_code LIKE 'P%' 
                ORDER BY product_id DESC 
                LIMIT 1";

        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            return $row['product_code'];
        }
        return null;
    }

    private function generateNextCode(?string $lastCode): string
    {
        if (!$lastCode) {
            return 'P0001';
        }

        $num = (int)substr($lastCode, 1); // Ambil angka, misalnya dari P0012 → 12
        $nextNum = $num + 1;

        return 'P' . str_pad($nextNum, 4, '0', STR_PAD_LEFT); // → P0013
    }


    public function updateProduct(array $data): array
    {
        $sql = "UPDATE {$this->tableProduct} SET product_name = ?, client_id = ?, category_id = ?, end_date = ? WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siisi", $data['product_name'], $data['client_id'], $data['category_id'], $data['end_date'], $data['product_id']);
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function deleteProduct($id)
    {
        $sql = "UPDATE {$this->tableProduct} SET is_deleted = 1 WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return ['status' => 'success'];
    }

    // ===============================
    // Product Version CRUD
    // ===============================

    public function findVersions(int $customerId): array
    {
        $sql = "SELECT v.*, p.product_name 
        FROM {$this->tableVersion} v
        LEFT JOIN ds_product_item p ON p.product_id = v.product_id
        WHERE v.is_deleted = 0";

        
        if ($customerId !== null) {
            $sql .= " AND v.customer_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        $versions = [];
        while ($row = $result->fetch_assoc()) {
            $versions[] = ProductVersion::fromArray($row);
        }

        return $versions;
    }

    public function findVersionById(int $versionId): ?array
    {
        $sql = "SELECT * FROM {$this->tableVersion} WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $versionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function createVersion(array $data)
    {
        $sql = "INSERT INTO {$this->tableVersion} (
                    product_id, version_name, resolution, file_path, origin_file_name, file_name,
                    file_size, duration, md5, thumbnail, created_by, customer_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "isssssiiisss",
            $data['product_id'],
            $data['version_name'],
            $data['resolution'],
            $data['file_path'],
            $data['origin_file_name'],
            $data['file_name'],
            $data['file_size'],
            $data['duration'],
            $data['md5'],
            $data['thumbnail'],
            $data['created_by'],
            $data['customer_id']
        );
        $stmt->execute();

        return [
            'status' => 'success',
            'id' => $stmt->insert_id,
            'version_name' => $data['version_name']
        ];
    }

    public function updateVersion(array $data)
    {
        $sql = "UPDATE {$this->tableVersion} SET
                    version_name = ?, resolution = ?, file_path = ?, origin_file_name = ?, file_name = ?,
                    file_size = ?, duration = ?, md5 = ?, thumbnail = ?, customer_id = ?
                WHERE version_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssiisii",
            $data['version_name'],
            $data['resolution'],
            $data['file_path'],
            $data['origin_file_name'],
            $data['file_name'],
            $data['file_size'],
            $data['duration'],
            $data['md5'],
            $data['thumbnail'],
            $data['customer_id'],
            $data['version_id']
        );
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function deleteVersion(int $versionId)
    {
        $sql = "UPDATE {$this->tableVersion} SET is_deleted = 1 WHERE version_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $versionId);
        $stmt->execute();

        return ['status' => 'success'];
    }

    public function uploadVersionFile(int $versionId, array $data)
    {
        $sql = "UPDATE {$this->tableVersion} 
                SET 
                    file_path = ?, 
                    origin_file_name = ?, 
                    file_name = ?, 
                    file_size = ?, 
                    thumbnail = ? 
                WHERE version_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssisi",
            $data['file_path'],
            $data['origin_file_name'],
            $data['file_name'],
            $data['file_size'],
            $data['thumbnail'],
            $versionId
        );

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'failed', 'message' => 'No rows updated'];
        }
    }

}
