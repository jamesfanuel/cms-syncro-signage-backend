<?php
class FormationController
{
    private FormationService $service;

    public function __construct(FormationService $service)
    {
        $this->service = $service;
    }

    public function find(?int $customerId = null): void
    {
        $clients = $this->service->find($customerId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $clients]);
    }


    public function findById(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $customer = $this->service->findById($id);
        echo json_encode(['status' => 'success', 'data' => $customer]);
    }

    public function create()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['customer_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid atau customer_id kosong']);
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
