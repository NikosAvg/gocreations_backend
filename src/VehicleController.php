<?php
declare(strict_types=1);
namespace App;



final class VehicleController
{
    public function __construct(
        private VehicleRepository $repository,
        private QueryValidator $queryValidator,
    ){}

    public function index(array $query): never
    {
        $errors = $this->queryValidator->validate($query);
        if ($errors !== []) {
            Response::json(['errors' => $errors], 400);
        }
        Response::json($this->repository->findAll($query));
    }
}
