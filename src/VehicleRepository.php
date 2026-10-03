<?php
declare(strict_types=1);

namespace App;
use PDO;

final class VehicleRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM vehicles ORDER BY id');
        $stmt->execute();

        return array_map($this->toArray(...), $stmt->fetchAll());
    }

    private function toArray(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => $row['model_name'],
            "type_id" => (int) $row['type_id'],
            'vehicle_type' => $row['vehicle_type'],
            'door' => (int) $row['door'],
            'transmission' => $row['transmission'],
            'fuel' => $row['fuel'],
            'price' => (float) $row['price'],
        ];
    }
}
