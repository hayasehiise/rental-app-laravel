<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()->whereHas('roles', function ($query) {
                    $query->whereNotIn('name', ['member', 'guest']);
                })
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email User'),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->getStateUsing(function ($record) {
                        $mapping = [
                            'admin' => 'Admin',
                            'staff_admin' => 'Staff Admin',
                            'finance_admin' => 'Finance Admin',
                        ];

                        return $record->roles->whereNotIn('name', ['member', 'guest'])
                            ->pluck('name')
                            ->map(fn($role) => $mapping[$role] ?? $role)
                            ->implode(', ');
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
