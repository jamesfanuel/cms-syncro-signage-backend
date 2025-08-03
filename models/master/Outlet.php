<?php

class Outlet
{
    public $outlet_id;
    public $outlet_name;
    public $contact;
    public $email;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;

    public function __construct($data = [])
    {
        $this->outlet_id     = $data['outlet_id'] ?? null;
        $this->outlet_name   = $data['outlet_name'] ?? '';
        $this->contact       = $data['contact'] ?? '';
        $this->email         = $data['email'] ?? '';
        $this->created_at    = $data['created_at'] ?? null;
        $this->created_by    = $data['created_by'] ?? null;
        $this->is_deleted    = isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false;
        $this->customer_id   = $data['customer_id'] ?? null;
    }

    public static function fromArray(array $data): Outlet
    {
        return new Outlet($data);
    }
}
