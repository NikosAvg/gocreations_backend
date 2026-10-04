<?php
declare(strict_types=1);
namespace App;
use JsonException;


final class VehicleController
{
    public function __construct(
        private VehicleRepository $repository,
        private QueryValidator $queryValidator,
        private VehicleValidator $vehicleValidator
    ){}

    public function index(array $query): never
    {
        $errors = $this->queryValidator->validate($query);
        if ($errors !== []) {
            Response::json(['errors' => $errors], 400);
        }
        Response::json($this->repository->findAll($query));
    }
    public function store(): never
    {
        $data = $this->readJsonBody();
        $this->validateOrFail($data);
        Response::json($this->repository->create($data), 201);
    }
    public function update(int $id): never
    {
        if($this->repository->findById($id)===null) {
            Response::json(['error' => 'Vehicle not found'], 404);
        }
        $data = $this->readJsonBody();
        $this->validateOrFail($data);
        $this->repository->update($id, $data);
        Response::json($this->repository->findById($id));
    }
    public function destroy(int $id): never
    {
        if (!$this -> repository->delete($id)){
            Response::json(['error' => 'Vehicle not found'], 404);
        }
        Response::json(null, 204);
    }

    private function readJsonBody(): array
    {
        try {
            $data = json_decode(
                file_get_contents('php://input') ?: '',
                true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            Response::json(['error' => 'Invalid JSON'], 400);
        }


        if(!is_array($data)) {
            Response::json(['error' => 'Body must be a JSON object'], 400);
        }
        return $data;
    }
    private function validateOrFail(array $data): void
    {
        $errors = $this->vehicleValidator->validate($data);
        if (!isset($errors['type_id']) && !$this->repository->typeExists($data['type_id'])) {
            $errors['type_id'] = 'does not exist';
        }
        if ($errors !== []) {
            Response::json(['errors' => $errors], 400);
        }
    }
}
