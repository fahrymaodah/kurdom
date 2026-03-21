<?php

declare(strict_types=1);

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
            self::Seller => 'Penjual',
            self::Courier => 'Kurir',
            self::Buyer => 'Pembeli',
        };
    }
}
