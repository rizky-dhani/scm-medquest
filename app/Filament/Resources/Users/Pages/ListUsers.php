<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Support\Str;
use Filament\Actions;
use Illuminate\Support\Facades\Hash;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        // Set default password & userId
                        $data['userId'] = (string) Str::orderedUuid();
                        $data['username'] = substr(strtolower(str_replace(' ', '.', $data['name'])), 0, 8);
                        $data['password'] = Hash::make('Scm2025!');
                        // Set default password change requirement for new users
                        $data['password_change_required'] = $data['password_change_required'] ?? true;
                        return $data;
                    }),
        ];
    }
}
