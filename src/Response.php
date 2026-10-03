<?php
declare(strict_types=1);

namespace App;

final class Response
{
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        if ($status !== 204){
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
