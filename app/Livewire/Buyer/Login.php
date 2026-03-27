<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $phone = '';

    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['phone' => $this->phone, 'password' => $this->password])) {
            $this->addError('phone', 'No. HP atau password salah.');

            return;
        }

        $user = Auth::user();
        if ($user->role !== UserRole::Buyer) {
            Auth::logout();
            $this->addError('phone', 'Akun ini bukan akun pembeli.');

            return;
        }

        if (! $user->is_active) {
            Auth::logout();
            $this->addError('phone', 'Akun Anda tidak aktif.');

            return;
        }

        session()->regenerate();

        $this->redirect(route('buyer.dashboard'));
    }

    public function render()
    {
        return view('livewire.buyer.login')
            ->layout('layouts.buyer', ['title' => 'Masuk']);
    }
}
