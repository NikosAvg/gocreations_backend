<?php
declare(strict_types=1);
namespace App;

use App\Enum\Fuel;
use App\Enum\Transmission;

final class VehicleValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        if (!$this->isNonEmptyString($data['model_name'] ?? null, 100)) {
            $errors['model_name'] = 'required, text up to 100 characters';
        }

        if (!is_int($data['type_id'] ?? null) || $data['type_id'] < 1) {
            $errors['type_id'] = 'required, must be a positive integer';
        }

        if (!$this->isNonEmptyString($data['vehicle_type'] ?? null, 50)) {
            $errors['vehicle_type'] = 'required, text up to 50 characters';
        }

        $doors = $data['doors'] ?? null;
        if (!is_int($doors) || $doors < 1 || $doors > 255) {
            $errors['doors'] = 'required, must be an integer greater than 0';
        }

        $transmission = $data['transmission'] ?? null;
        if (!is_string($transmission) || Transmission::tryFrom($transmission) === null) {
            $errors['transmission'] = 'required, must be manual or automatic';
        }

        $fuel = $data['fuel'] ?? null;
        if (!is_string($fuel) || Fuel::tryFrom($fuel) === null) {
            $errors['fuel'] = 'required, must be petrol, diesel, hybrid or electric';
        }

        $price = $data['price'] ?? null;
        if (!(is_int($price) || is_float($price)) || $price < 0) {
            $errors['price'] = 'required, must be a number >= 0';
        }
        return $errors;
    }
    private function isNonEmptyString(mixed $value, int $maxLength): bool
    {
        return is_string($value) && trim($value) !== '' && strlen($value) <= $maxLength;
    }
}

?>
