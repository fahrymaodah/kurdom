<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class MapPicker extends Component
{
    #[Modelable]
    public ?float $latitude = null;

    #[Modelable]
    public ?float $longitude = null;

    public ?string $address = null;

    public int $zoom = 15;

    public bool $readOnly = false;

    public string $mapId = 'map-picker';

    public string $locationEvent = 'location-updated';

    // Default: center of Dompu, NTB
    public float $defaultLat = -8.5365;

    public float $defaultLng = 118.4633;

    public function mount(
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $address = null,
        int $zoom = 15,
        bool $readOnly = false,
        string $mapId = 'map-picker',
        string $locationEvent = 'location-updated',
    ): void {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->address = $address;
        $this->zoom = $zoom;
        $this->readOnly = $readOnly;
        $this->mapId = $mapId;
        $this->locationEvent = $locationEvent;
    }

    public function updateLocation(float $lat, float $lng, ?string $address = null): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->address = $address;
        $this->dispatch($this->locationEvent, lat: $lat, lng: $lng, address: $address);
    }

    public function useMyLocation(): void
    {
        $this->dispatch('request-geolocation');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.map-picker');
    }
}
