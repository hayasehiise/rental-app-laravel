<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->required(fn($context) => $context === 'create')
                    ->minLength(6)
                    ->dehydrateStateUsing(fn($state) => $state ? bcrypt($state) : null),
                Select::make('role')
                    ->label('Role')
                    ->options(
                        Role::whereNotIn('name', ['member', 'guest'])->pluck('name')
                    )
                    ->required(),
            ]);
    }
}
