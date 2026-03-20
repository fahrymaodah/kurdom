<?php

namespace App\Filament\Auth;

use App\Enums\UserRole;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('No. Telepon')
            ->tel()
            ->required()
            ->maxLength(20)
            ->unique($this->getUserModel());
    }

    protected function handleRegistration(array $data): Model
    {
        $panelId = Filament::getCurrentOrDefaultPanel()->getId();

        $role = match ($panelId) {
            'seller' => UserRole::Seller,
            'courier' => UserRole::Courier,
            default => UserRole::Buyer,
        };

        return $this->getUserModel()::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
