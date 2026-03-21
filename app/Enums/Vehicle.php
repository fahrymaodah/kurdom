<?php

declare(strict_types=1);

namespace App\Enums;

enum Vehicle: string
{
    case Motorcycle = 'motorcycle';
    case Car = 'car';

    public function label(): string
    {
        return match ($this) {
            self::Motorcycle => 'Motor',
            self::Car => 'Mobil',
        };
    }
}
