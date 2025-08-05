<?php
class Order
{
    public $order_id;
    public $campaign_id;
    public $order_name;
    public $duration;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;

    public $order_items = [];

    public function __construct($data = [])
    {
        if (isset($data['order_id'])) $this->order_id = $data['order_id'];
        if (isset($data['campaign_id'])) $this->campaign_id = $data['campaign_id'];
        if (isset($data['order_name'])) $this->order_name = $data['order_name'];
        if (isset($data['duration'])) $this->duration = $data['duration'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        if (isset($data['created_by'])) $this->created_by = $data['created_by'];
        if (isset($data['is_deleted'])) $this->is_deleted = $data['is_deleted'];
        if (isset($data['customer_id'])) $this->customer_id = $data['customer_id'];

        if (isset($this->order_id)) {
            $orderItemRepo = new OrderItemRepository($conn);
            $this->order_items = $orderItemRepo->findByOrderId($this->order_id);
        }
    }
}


class OrderItem
{
    public $order_item_id;
    public $campaign_id;
    public $order_id;
    public $outlet_id;
    public $start_date;
    public $end_date;
    public $position_id;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;
    public ?string $campaign_name = null;
    public ?string $order_name = null;
    public ?string $outlet_name = null;

    public function __construct($data = [])
    {
        if (isset($data['order_item_id'])) $this->order_item_id = $data['order_item_id'];
        if (isset($data['campaign_id'])) $this->campaign_id = $data['campaign_id'];
        if (isset($data['order_id'])) $this->order_id = $data['order_id'];
        if (isset($data['outlet_id'])) $this->outlet_id = $data['outlet_id'];
        if (isset($data['start_date'])) $this->start_date = $data['start_date'];
        if (isset($data['end_date'])) $this->end_date = $data['end_date'];
        if (isset($data['position_id'])) $this->position_id = $data['position_id'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        if (isset($data['created_by'])) $this->created_by = $data['created_by'];
        if (isset($data['is_deleted'])) $this->is_deleted = $data['is_deleted'];
        if (isset($data['customer_id'])) $this->customer_id = $data['customer_id'];
        if (isset($data['campaign_name'])) $this->campaign_name = $data['campaign_name'];
        if (isset($data['order_name'])) $this->order_name = $data['order_name'];
        if (isset($data['outlet_name'])) $this->outlet_name = $data['outlet_name'];
    }

    public static function fromArray(array $data): OrderItem
    {
        return new OrderItem($data);
    }
}
?>
