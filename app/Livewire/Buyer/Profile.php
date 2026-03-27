<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $address_text = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $saved = false;

    public bool $passwordChanged = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->address_text = $user->address_text ?? '';
        $this->latitude = $user->latitude ? (float) $user->latitude : null;
        $this->longitude = $user->longitude ? (float) $user->longitude : null;
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . Auth::id()],
            'address_text' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        Auth::user()->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address_text' => $validated['address_text'] ?: null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        $this->saved = true;
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'Password saat ini salah.');

            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordChanged = true;
    }

    public function render()
    {
        return view('livewire.buyer.profile')
            ->layout('layouts.buyer', ['title' => 'Profil']);
    }
}
