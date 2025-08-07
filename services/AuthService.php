<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class AuthService
{
    private UserRepository $userRepo;

    public function __construct(mysqli $mysqli)
    {
        $this->userRepo = new UserRepository($mysqli);
    }

    public function register(array $data): User|string
    {
        $existingUser = $this->userRepo->findByUsername($data['user_name']);
        if ($existingUser) {
            return 'USER_EXISTS';
        }

        $hashedPassword = password_hash("syncrosignage", PASSWORD_BCRYPT);

        $createdBy = $data['created_by'] ?? null;

        $user = new User([
            'user_name'   => $data['user_name'],
            'password'    => $hashedPassword,
            'is_admin'    => $data['is_admin'],
            'customer_id' => $data['customer_id'],
            'expired_at'  => $data['expired_at'],
            'created_by'  => $createdBy
        ]);

        $success = $this->userRepo->create($user);

        if (!$success) {
            return 'CREATE_FAILED';
        }

        return $user;
    }


    public function login(string $loginId, string $password)
    {
        // Deteksi input email atau username
        $user = filter_var($loginId, FILTER_VALIDATE_EMAIL)
            ? $this->userRepo->findByEmail($loginId)
            : $this->userRepo->findByUsername($loginId);

        if (!$user) {
            return 'USERNAME_NOT_FOUND';
        }

        if (!empty($user->expired_at) && strtotime($user->expired_at) < time()) {
            return 'USERNAME_EXPIRED';
        }

        if (!password_verify($password, $user->password)) {
            return 'WRONG_PASSWORD';
        }

        return $user;
    }

    public function find(): array
    {
        return $this->userRepo->find();
    }

    public function update(array $data): array
    {
        return $this->userRepo->update($data);
    }

    public function delete(int $id): array
    {
        return $this->userRepo->delete($id);
    }
}
