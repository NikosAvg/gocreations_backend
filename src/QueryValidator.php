<?php
declare(strict_types=1);

// NOTE TO SELF
// always chech is_string() PHP does a weird thing of allowing to sent ?sort[]=x
// which makes $query['sort'] an array

namespace App;
use App\Enum\Transmission;
final class QueryValidator
{
    public function validate(array $query): array
    {
        $errors = [];
        foreach (['price_min', 'price_max'] as $key) {
            if (isset($query[$key]) && !$this->isNonNegativeNumber($query[$key])) {
                $errors[$key] = 'Price must be a non-negative number';
            }
        }

        // We only compare min and max if both are present and valid
        if (isset($query['price_min'],$query['price_max']) && !isset($errors['price_min'])
        && !isset($errors['price_max']) && (float) $query['price_min'] >(float) $query['price_max']){
            $errors['price_max'] = 'Price max must be greater than or equal to price min';
        }

        if (isset($query['transmission'])
            && (!is_string($query['transmission'])
            || Transmission::tryFrom($query['transmission']) === null)) {
            $errors['transmission'] = 'must be manual or automatic';
        }
        if (isset($query['type_id'])
            && filter_var($query['type_id'], FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]) === false) {
            $errors['type_id'] = 'must be a positive integer';
        }

        if (isset($query['sort'])
            && (!is_string($query['sort'])
                || !array_key_exists($query['sort'], VehicleRepository::SORTS))) {
            $errors['sort'] = 'must be one of: '
                . implode(', ', array_keys(VehicleRepository::SORTS));
        }

        return $errors;
    }

    private function isNonNegativeNumber(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT,
            ['options' => ['min_range' => 0]]) !== false;
    }
}
