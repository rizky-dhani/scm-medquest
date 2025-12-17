<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Notification Details')
                    ->schema([
                        TextInput::make('notification_type')
                            ->label('Notification Type')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('mailable_class')
                            ->label('Mailable Class')
                            ->maxLength(255),

                        TextInput::make('recipient_email')
                            ->label('Recipient Email')
                            ->email()
                            ->maxLength(255),

                        Toggle::make('was_successful')
                            ->label('Was Successful')
                            ->inline(false),

                        Textarea::make('error_message')
                            ->label('Error Message')
                            ->rows(3)
                            ->maxLength(65535),

                        TextInput::make('sent_at')
                            ->label('Sent At'),

                        Textarea::make('data')
                            ->label('Data Payload')
                            ->rows(5),

                        TextInput::make('notifiable_type')
                            ->label('Notifiable Type')
                            ->maxLength(255),

                        TextInput::make('notifiable_id')
                            ->label('Notifiable ID')
                            ->numeric(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->orderByDesc('created_at'))
            ->columns([

                TextColumn::make('notification_type')
                    ->label('Notification Type')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => ucwords(str_replace('_', ' ', $record->notification_type))),

                TextColumn::make('mailable_class')
                    ->label('Mailable Class')
                    ->searchable(),

                TextColumn::make('recipient_email')
                    ->label('Recipient Email')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('notification_type')
                    ->options([
                        'temperature_deviation' => 'Temperature Deviation',
                        'high_priority_deviation' => 'High Priority Deviation',
                        'inventory_alert' => 'Inventory Alert',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),

                SelectFilter::make('was_successful')
                    ->label('Success Status')
                    ->options([
                        '1' => 'Successful',
                        '0' => 'Failed',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // Bulk actions can be added here if needed
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationLogs::route('/'),
            'view' => Pages\ViewNotificationLog::route('/{record}'),
        ];
    }
}