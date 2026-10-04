<?php
declare(strict_types=1);

namespace App\Enum;

enum Fuel: string
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Hybrid = 'hybrid';
    case Electric = 'electric';
}
?>
