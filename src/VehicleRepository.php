<?php
declare(strict_types=1);

namespace App;
use PDO;

final class VehicleRepository
{
    public const SORTS = [
        'name_asc' => 'model_name ASC, id ASC',
        'name_desc' => 'model_name DESC, id ASC',
        'price_asc' => 'price ASC, id ASC',
        'price_desc' => 'price DESC, id ASC',
    ];

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
        $sort = $filters['sort'] ?? null;
        $sql .= ' ORDER BY ' . (self::SORTS[$sort] ?? 'id ASC');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map($this->toArray(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->toArray($row);
    }
    public function create(array $data): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO vehicles (model_name, type_id, vehicle_type, doors, transmission, fuel, price) VALUES (:model_name, :type_id, :vehicle_type, :doors, :transmission, :fuel, :price)');
        $stmt->execute($data);
        return $this->findById((int) $this->pdo->lastInsertId());
    }
    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE vehicles SET model_name = :model_name, type_id = :type_id, vehicle_type = :vehicle_type, doors = :doors, transmission = :transmission, fuel = :fuel, price = :price WHERE id = :id');
        $stmt->execute([...$this->params($data), 'id' => $id]);
    }
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM vehicles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0; // false if no rows deleted 404
    }
    public function typeExists(int $typeId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM vehicle_types WHERE id = :id');
        $stmt->execute(['id' => $typeId]);
        return $stmt->fetchColumn()!==false;
    }

    private function params(array $data): array
    {
        return [
            'model_name' => $data['model_name'],
            'type_id' => $data['type_id'],
            'vehicle_type' => $data['vehicle_type'],
            'doors' => $data['doors'],
            'transmission' => $data['transmission'],
            'fuel' => $data['fuel'],
            'price' => $data['price'],
        ];
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
