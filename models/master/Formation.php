<?php

class Formation
{
    public $screen_id;
    public $outlet_id;
    public $screen_name;
    public $screen_description;
    public $screen_function;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;
    public ?string $outlet_name = null;

    public function __construct($data = [])
    {
        $this->screen_id           = $data['screen_id'] ?? null;
        $this->outlet_id           = $data['outlet_id'] ?? null;
        $this->screen_name         = $data['screen_name'] ?? '';
        $this->screen_description  = $data['screen_description'] ?? '';
        $this->screen_function     = $data['screen_function'] ?? '';
        $this->created_at          = $data['created_at'] ?? null;
        $this->created_by          = $data['created_by'] ?? null;
        $this->is_deleted          = isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false;
        $this->customer_id         = $data['customer_id'] ?? null;
        $this->outlet_name         = $data['outlet_name'] ?? null;
    }
    
    public static function fromArray(array $data): Formation
    {
        return new Formation($data);
    }
}
