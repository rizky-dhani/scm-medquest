<?php

namespace App\Filament\Resources\NotificationSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Configuration')
                    ->description('General configuration for this notification event.')
                    ->schema([
                        TextInput::make('event_key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null),
                        TextInput::make('event_name')
                            ->required(),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Recipients')
                    ->description('Choose how to identify recipients for this notification.')
                    ->schema([
                        Select::make('roles')
                            ->label('By Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('All users with these roles will receive the notification.'),
                        Select::make('users')
                            ->label('By Users')
                            ->relationship('users', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('These specific users will receive the notification.'),
                    ])->columns(2),
            ]);
    }
}