<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Addresses extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $label = '';

    public string $address_text = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public bool $is_default = false;

    #[Computed]
    public function addresses()
    {
        return Auth::user()->addresses()->orderByDesc('is_default')->orderBy('label')->get();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'label', 'address_text', 'latitude', 'longitude', 'is_default']);
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $this->editingId = $address->id;
        $this->label = $address->label;
        $this->address_text = $address->address_text;
        $this->latitude = (float) $address->latitude;
        $this->longitude = (float) $address->longitude;
        $this->is_default = $address->is_default;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'label' => ['required', 'string', 'max:100'],
            'address_text' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'is_default' => ['boolean'],
        ]);

        if ($validated['is_default']) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        if ($this->editingId) {
            Auth::user()->addresses()->where('id', $this->editingId)->update($validated);
        } else {
            Auth::user()->addresses()->create($validated);
        }

        $this->reset(['showForm', 'editingId', 'label', 'address_text', 'latitude', 'longitude', 'is_default']);
        unset($this->addresses);
    }

    public function delete(int $id): void
    {
        Auth::user()->addresses()->where('id', $id)->delete();
        unset($this->addresses);
    }

    public function cancel(): void
    {
        $this->reset(['showForm', 'editingId', 'label', 'address_text', 'latitude', 'longitude', 'is_default']);
    }

    public function render()
    {
        return view('livewire.buyer.addresses')
            ->layout('layouts.buyer', ['title' => 'Alamat Tersimpan']);
    }
}
