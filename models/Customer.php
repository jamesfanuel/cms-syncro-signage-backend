<?php

class Customer
{
    public int $customer_id;
    public string $customer_name;
    public ?string $email;
    public ?string $licence_date;
    public string $created_at;
    public ?string $created_by;
    public bool $is_deleted;

    public const TABLE_NAME = 'ds_customer';

    public function __construct(array $data)
    {
        $this->customer_id = (int)($data['customer_id'] ?? 0);
        $this->customer_name = $data['customer_name'] ?? '';
        $this->email = $data['email'] ?? null;
        $this->licence_date = $data['licence_date'] ?? null;
        $this->created_at = $data['created_at'] ?? '';
        $this->created_by = $data['created_by'] ?? null;
        $this->is_deleted = (bool)($data['is_deleted'] ?? false);
    }

    // Tambahkan ini untuk digunakan oleh repository
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    // Opsional: untuk mengembalikan data sebagai array
    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'email' => $this->email,
            'licence_date' => $this->licence_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'is_deleted' => $this->is_deleted,
        ];
    }
}
