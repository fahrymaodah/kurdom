<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderSource: string
{
    case WaFb = 'wa_fb';
    case Storefront = 'storefront';

    public function label(): string
    {
        return match ($this) {
            self::WaFb => 'WA / Facebook',
            self::Storefront => 'Storefront',
        };
    }
}
