<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    protected string $view = 'filament.pages.edit-profile';

    public function getTitle(): string
    {
        return 'Edit Profile';
    }

    public function getSubheading(): ?string
    {
        return 'Update your personal information and password.';
    }

    public function getRedirectUrl(): ?string
    {
        return '/';
    }

    public function getSavedNotificationTitle(): string
    {
        return 'Profile updated successfully';
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update user information
        $record->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // If password was provided, update it
        if (!empty($data['password'])) {
            $record->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return $record;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),
                Section::make('Change Password')
                    ->schema([
                        $this->getPasswordFormComponent()
                            ->helperText('Leave blank to keep current password.'),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->description('Enter a new password if you want to change it.'),
            ]);
    }
}
