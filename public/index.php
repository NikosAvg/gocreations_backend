<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Database;

function jsonResponse(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    if($status !== 204){
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];
$path = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

if ($path === '/vehicles'){
    if ($method === 'GET') {
        $pdo = Database::connect();
        $count = $pdo -> query('SELECT COUNT(*) FROM vehicles')->fetchColumn();
        jsonResponse(['vehicles_in_db' => (int) $count]);
    }
    jsonResponse(['error' => 'Method not allowed'], 405);
}

jsonResponse(['error' => 'Not found'], 404);
?>
