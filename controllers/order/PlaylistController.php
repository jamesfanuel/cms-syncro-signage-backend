<?php
class PlaylistController
{
    private PlaylistService $service;

    public function __construct(PlaylistService $service)
    {
        $this->service = $service;
    }

    public function find(?int $customerId = null, ?int $outletId = null): void
    {
        $playlists = $this->service->find($customerId, $outletId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $playlists]);
    }


    public function findById(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $campaign = $this->service->findById($id);
        echo json_encode(['status' => 'success', 'data' => $campaign]);
    }

    public function create()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $result = $this->service->create($payload);
        echo json_encode($result);
    }

    public function update(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['screen_id'] = $id;
        $result = $this->service->update($payload);
        echo json_encode($result);
    }

    public function delete(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $result = $this->service->delete($id);
        echo json_encode($result);
    }
}
