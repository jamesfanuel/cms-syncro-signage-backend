<?php
class Playlist
{
    public $playlist_id;
    public $play_date;
    public $order_id;
    public $order_name;
    public $outlet_id;
    public $screen_id;
    public $version_id;
    public $file_path;
    public $order_duration;
    public $created_at;
    public $created_by;
    public $is_deleted;
    public $customer_id;
    public ?string $outlet_name = null;
    public ?string $screen_name = null;
    public ?string $version_name = null;

    public function __construct(array $data = [])
    {
        $this->playlist_id     = $data['playlist_id'] ?? null;
        $this->play_date       = $data['play_date'] ?? null;
        $this->order_id        = $data['order_id'] ?? null;
        $this->order_name      = $data['order_name'] ?? null;
        $this->outlet_id       = $data['outlet_id'] ?? null;
        $this->screen_id       = $data['screen_id'] ?? null;
        $this->version_id      = $data['version_id'] ?? null;
        $this->file_path       = $data['file_path'] ?? null;
        $this->order_duration  = $data['order_duration'] ?? null;
        $this->created_at      = $data['created_at'] ?? null;
        $this->created_by      = $data['created_by'] ?? null;
        $this->is_deleted      = $data['is_deleted'] ?? 0;
        $this->customer_id     = $data['customer_id'] ?? null;
        $this->outlet_name     = $data['outlet_name'] ?? null;
        $this->screen_name     = $data['screen_name'] ?? null;
        $this->version_name    = $data['version_name'] ?? null;
    }

    public static function fromArray(array $data): Playlist
    {
        return new Playlist($data);
    }
}
