<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Seller = 'seller';
    case Courier = 'courier';
    case Buyer = 'buyer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Seller => 'Seller',
            self::Courier => 'Courier',
            self::Buyer => 'Buyer',
        };
    }
}
