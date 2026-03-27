<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $address_text = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'address_text' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Buyer,
            'address_text' => $validated['address_text'] ?: null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        Auth::login($user);

        $this->redirect(route('buyer.dashboard'));
    }

    public function render()
    {
        return view('livewire.buyer.register')
            ->layout('layouts.buyer', ['title' => 'Daftar']);
    }
}
