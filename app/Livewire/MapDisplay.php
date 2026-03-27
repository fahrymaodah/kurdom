<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class MapDisplay extends Component
{
    public ?float $pickupLat = null;

    public ?float $pickupLng = null;

    public ?string $pickupLabel = 'Pickup';

    public ?float $deliveryLat = null;

    public ?float $deliveryLng = null;

    public ?string $deliveryLabel = 'Tujuan';

    public string $mapId = 'map-display';

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.map-display');
    }
}
