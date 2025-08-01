<?php
class ProductCategory {
    public $category_id, $category_name, $customer_id, $created_at, $created_by, $is_deleted;
    public function __construct($data) {
        $this->category_id   = $data['category_id'] ?? null;
        $this->category_name = $data['category_name'] ?? null;
        $this->customer_id   = $data['customer_id'] ?? null;
        $this->created_at    = $data['created_at'] ?? null;
        $this->created_by    = $data['created_by'] ?? null;
        $this->is_deleted    = $data['is_deleted'] ?? 0;
    }
    public static function fromArray(array $data): ProductCategory {
        return new ProductCategory($data);
    }
}

class Product {
    public $product_id, $product_code, $product_name, $client_id, $category_id, $end_date,
           $created_at, $created_by, $is_deleted, $customer_id;
    public function __construct($data) {
        $this->product_id    = $data['product_id'] ?? null;
        $this->product_code  = $data['product_code'] ?? null;
        $this->product_name  = $data['product_name'] ?? null;
        $this->client_id     = $data['client_id'] ?? null;
        $this->category_id   = $data['category_id'] ?? null;
        $this->end_date      = $data['end_date'] ?? null;
        $this->created_at    = $data['created_at'] ?? null;
        $this->created_by    = $data['created_by'] ?? null;
        $this->is_deleted    = $data['is_deleted'] ?? 0;
        $this->customer_id   = $data['customer_id'] ?? null;
    }
    public static function fromArray(array $data): Product {
        return new Product($data);
    }
}

class ProductVersion {
    public ?int $version_id;
    public ?int $product_id;
    public string $version_name;
    public ?string $resolution;
    public ?string $file_path;
    public ?string $origin_file_name;
    public ?string $file_name;
    public ?int $file_size;
    public ?int $duration;
    public ?string $md5;
    public ?string $thumnail;
    public ?string $created_at;
    public ?string $created_by;
    public bool $is_deleted;
    public ?int $customer_id;
    public ?string $product_name = null;

    public function __construct(array $data)
    {
        $this->version_id        = $data['version_id'] ?? null;
        $this->product_id        = $data['product_id'] ?? null;
        $this->version_name      = $data['version_name'] ?? '';
        $this->resolution        = $data['resolution'] ?? null;
        $this->file_path         = $data['file_path'] ?? null;
        $this->origin_file_name  = $data['origin_file_name'] ?? null;
        $this->file_name         = $data['file_name'] ?? null;
        $this->file_size         = isset($data['file_size']) ? (int)$data['file_size'] : null;
        $this->duration          = isset($data['duration']) ? (int)$data['duration'] : null;
        $this->md5               = $data['md5'] ?? null;
        $this->thumnail          = $data['thumnail'] ?? null;
        $this->created_at        = $data['created_at'] ?? null;
        $this->created_by        = $data['created_by'] ?? null;
        $this->is_deleted        = isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false;
        $this->customer_id       = $data['customer_id'] ?? null;
        $this->product_name = $data['product_name'] ?? null;
    }
    
    public static function fromArray(array $data): ProductVersion {
        return new ProductVersion($data);
    }
}
