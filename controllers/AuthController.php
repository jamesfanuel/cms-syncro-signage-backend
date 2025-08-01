<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../helpers/response.php';

use Helpers\Response;

class AuthController
{
    private $authService;

    public function __construct($mysqli)
    {
        $this->authService = new AuthService($mysqli);
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            return Response::error('Invalid JSON', 400);
        }

        $result = $this->authService->register($data);

        if ($result === 'EMAIL_EXISTS') {
            return Response::error('Email already registered', 409);
        }

        return Response::success(['user' => $result], 'Registration successful');
    }

    public function login()
    {
        // Ambil data JSON dari body
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($login) || empty($password)) {
            return Response::error('Login dan password wajib diisi', 400);
        }

        // Proses login dengan authService
        $result = $this->authService->login($login, $password);

        // Handle berbagai kemungkinan hasil
        if ($result === 'USERNAME_NOT_FOUND') {
            return Response::error('Username tidak terdaftar', 404);
        }

        if ($result === 'WRONG_PASSWORD') {
            return Response::error('Password salah', 401);
        }

        if ($result === 'USERNAME_EXPIRED') {
            return Response::error('Username sudah kedaluwarsa', 403);
        }

        // Jika berhasil login
        return Response::success([
            'user_id'       => $result->user_id,
            'user_name'     => $result->user_name,
            'email'         => $result->email,
            'is_admin'      => $result->is_admin,
            'customer_id'   => $result->customer_id,
            'token'         => 'dummy-token' // Ganti dengan JWT asli nanti
        ], 'Login berhasil');
    }


    public function find()
    {
        $data = $this->authService->find();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function update(int $id)
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payload kosong']);
            return;
        }

        $payload['id'] = $id; // Inject ID dari URL ke payload
        $result = $this->authService->update($payload);
        echo json_encode($result);
    }

    public function delete(int $id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID wajib diisi']);
            return;
        }

        $result = $this->authService->delete($id);
        echo json_encode($result);
    }

}
