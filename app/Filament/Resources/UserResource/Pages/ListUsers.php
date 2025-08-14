<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Hash;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Set default password & userId
                        $data['userId'] = (string) \Illuminate\Support\Str::orderedUuid();
                        $data['username'] = substr(strtolower(str_replace(' ', '.', $data['name'])), 0, 8);
                        $data['password'] = Hash::make('Scm2025!');
                        // Set default password change requirement for new users
                        $data['password_change_required'] = $data['password_change_required'] ?? true;
                        return $data;
                    }),
        ];
    }
}
