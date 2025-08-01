<?php

namespace Helpers;

class Response {
    public static function success($data, $message = '', $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public static function error($message = 'Terjadi kesalahan', $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public static function json($data, $message = '', $status = 200) {
        self::success($data, $message, $status);
    }
}

