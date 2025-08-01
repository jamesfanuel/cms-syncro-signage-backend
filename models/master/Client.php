<?php

class Client
{
    public $client_id;
    public $client_code;
    public $client_name;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;

    public function __construct($data = [])
    {
        $this->client_id    = $data['client_id'] ?? null;
        $this->client_code  = $data['client_code'] ?? '';
        $this->client_name  = $data['client_name'] ?? '';
        $this->created_at   = $data['created_at'] ?? null;
        $this->created_by   = $data['created_by'] ?? null;
        $this->is_deleted   = isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false;
        $this->customer_id  = $data['customer_id'] ?? null;
    }

    public static function fromArray(array $data): Client
    {
        return new Client($data);
    }
}
