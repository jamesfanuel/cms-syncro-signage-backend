<?php

class License
{
    public int $license_id;
    public string $license_code;
    public int $customer_id;
    public int $outlet_id;
    public int $screen_id;
    public string $expired_at;
    public string $created_at;
    public ?int $created_by;
    public bool $is_deleted;
    public ?string $customer_name = null;
    public ?string $outlet_name = null;
    public ?string $screen_name = null;

    public const TABLE_NAME = 'ds_license';

    public function __construct(array $data)
    {
        $this->license_id   = (int)($data['license_id'] ?? 0);
        $this->license_code = $data['license_code'] ?? '';
        $this->customer_id  = (int)($data['customer_id'] ?? 0);
        $this->outlet_id    = (int)($data['outlet_id'] ?? 0);
        $this->screen_id    = (int)($data['screen_id'] ?? 0);
        $this->expired_at = $data['expired_at'] ?? '';
        $this->created_at   = $data['created_at'] ?? '';
        $this->created_by   = isset($data['created_by']) ? (int)$data['created_by'] : null;
        $this->is_deleted   = (bool)($data['is_deleted'] ?? false);
        $this->customer_name   = $data['customer_name'] ?? '';
        $this->outlet_name   = $data['outlet_name'] ?? '';
        $this->screen_name   = $data['screen_name'] ?? '';
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'license_id'   => $this->license_id,
            'license_code' => $this->license_code,
            'customer_id'  => $this->customer_id,
            'outlet_id'    => $this->outlet_id,
            'screen_id'    => $this->screen_id,
            'expired_at' => $this->expired_at,
            'created_at'   => $this->created_at,
            'created_by'   => $this->created_by,
            'is_deleted'   => $this->is_deleted,
            'customer_name'   => $this->customer_name,
            'outlet_name'  => $this->outlet_name,
            'screen_name'   => $this->screen_name,
        ];
    }
}
