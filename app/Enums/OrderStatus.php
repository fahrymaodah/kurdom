<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case CourierAssigned = 'courier_assigned';
    case PickedUp = 'picked_up';
    case InDelivery = 'in_delivery';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::CourierAssigned => 'Kurir Ditugaskan',
            self::PickedUp => 'Diambil',
            self::InDelivery => 'Dalam Pengiriman',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::CourierAssigned => 'warning',
            self::PickedUp => 'warning',
            self::InDelivery => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Get valid next statuses from the current status.
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::New => [self::CourierAssigned, self::Cancelled],
            self::CourierAssigned => [self::PickedUp, self::Cancelled],
            self::PickedUp => [self::InDelivery],
            self::InDelivery => [self::Completed],
            self::Completed => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }
}
