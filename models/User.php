<?php

class User
{
    public const TABLE_NAME = 'ds_user';
    
    public int $user_id;
    public string $user_name;
    public string $password;
    public ?string $email = null;
    public ?int $customer_id = null;
    public ?string $created_at = null;
    public ?string $created_by = null;
    public ?string $expired_at = null;   // ✅ Baru
    public bool $is_deleted = false;
    public ?int $is_admin = null;        // ✅ Baru

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->user_id     = (int)($data['user_id'] ?? 0);
            $this->user_name   = $data['user_name'] ?? '';
            $this->password    = $data['password'] ?? '';
            $this->email       = $data['email'] ?? null;
            $this->customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
            $this->created_at  = $data['created_at'] ?? null;
            $this->created_by  = $data['created_by'] ?? null;
            $this->expired_at  = $data['expired_at'] ?? null; // ✅ Baru
            $this->is_deleted  = isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false;
            $this->is_admin    = isset($data['is_admin']) ? (int)$data['is_admin'] : null; // ✅ Baru
        }
    }
}
