<?php
declare(strict_types=1);

namespace App;
use PDO;

final class VehicleRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(array $filters = []): array
    {
        $where = [];
        $params = [];
        if(isset($filters['price_min'])) {
            $where[] = 'price >= :price_min';
            $params['price_min'] = (float) $filters['price_min'];
        }
        if(isset($filters['price_max'])) {
            $where[] = 'price <= :price_max';
            $params['price_max'] = (float) $filters['price_max'];
        }
        if (isset($filters['transmission'])) {
                $where[] = 'transmission = :transmission';
                $params['transmission'] = $filters['transmission'];
        }
        if (isset($filters['type_id'])) {
            $where[] = 'type_id = :type_id';
            $params['type_id'] = $filters['type_id'];
        }
        $sql = 'SELECT * FROM vehicles';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map($this->toArray(...), $stmt->fetchAll());
    }

    private function toArray(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => $row['model_name'],
            "type_id" => (int) $row['type_id'],
            'vehicle_type' => $row['vehicle_type'],
            'doors' => (int) $row['doors'],
            'transmission' => $row['transmission'],
            'fuel' => $row['fuel'],
            'price' => (float) $row['price'],
        ];
    }
}
