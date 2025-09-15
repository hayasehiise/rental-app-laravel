<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $state = $this->form->getState();

        $role = $state['role'];

        $this->record->syncRoles([$role]);
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
