<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Database;
use App\Response;
use App\VehicleController;
use App\VehicleRepository;

$method = $_SERVER["REQUEST_METHOD"];
$path = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

$controller = new VehicleController(
    new VehicleRepository(Database::connect())
);

if ($path === '/vehicles'){
    if ($method === 'GET') {
        $controller->index();
    } else {
        Response::json(['error' => 'Method not allowed'], 405);
    }
}

Response::json(['error' => 'Not found'], 404);
?>
