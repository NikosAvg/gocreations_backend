<?php
declare(strict_types=1);
namespace App;



final class VehicleController
{
    public function __construct(private VehicleRepository $repository){}

    public function index(array $query): never
    {
        Response::json($this->repository->findAll($query));
    }
}
