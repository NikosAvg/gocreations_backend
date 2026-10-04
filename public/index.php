<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Database;
use App\Response;
use App\VehicleController;
use App\VehicleRepository;
use App\QueryValidator;
use App\VehicleValidator;

$method = $_SERVER["REQUEST_METHOD"];
$path = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

$controller = new VehicleController(
    new VehicleRepository(Database::connect()),
    new QueryValidator(),
    new VehicleValidator()
);

if ($path === '/vehicles'){
    match ($method){
        'GET'   => $controller->index($_GET),
        'POST'  => $controller->store($_POST),
        default => Response::json(['error' => 'Method not allowed'], 405),
    };
}

if (preg_match('/^\/vehicles\/\d+$/', $path, $matches)) {
    $id = (int) $matches[1];
    match ($method){
        'PUT'   => $controller->update($id),
        'DELETE' => $controller->destroy($id),
        default => Response::json(['error' => 'Method not allowed'], 405),
    };
}

Response::json(['error' => 'Not found'], 404);
?>
