<?php
class Campaign
{
    public $campaign_id;
    public $campaign_name;
    public $client_id;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $start_date;
    public $end_date;
    public $customer_id;
    public ?string $client_name = null;

    public function __construct($data = [])
    {
        if (isset($data['campaign_id'])) $this->campaign_id = $data['campaign_id'];
        if (isset($data['campaign_name'])) $this->campaign_name = $data['campaign_name'];
        if (isset($data['client_id'])) $this->client_id = $data['client_id'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        if (isset($data['created_by'])) $this->created_by = $data['created_by'];
        if (isset($data['is_deleted'])) $this->is_deleted = $data['is_deleted'];
        if (isset($data['start_date'])) $this->start_date = $data['start_date'];
        if (isset($data['end_date'])) $this->end_date = $data['end_date'];
        if (isset($data['customer_id'])) $this->customer_id = $data['customer_id'];
        if (isset($data['client_name'])) $this->client_name = $data['client_name'];
    }

    public static function fromArray(array $data): Campaign
    {
        return new Campaign($data);
    }
}
