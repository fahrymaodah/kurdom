<?php

namespace App\Enums;

enum Vehicle: string
{
    case Motorcycle = 'motorcycle';
    case Car = 'car';

    public function label(): string
    {
        return match ($this) {
            self::Motorcycle => 'Motorcycle',
            self::Car => 'Car',
        };
    }
}
