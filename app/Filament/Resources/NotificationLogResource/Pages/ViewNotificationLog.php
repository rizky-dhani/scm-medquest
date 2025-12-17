<?php

namespace App\Filament\Resources\NotificationLogResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use App\Filament\Resources\NotificationLogResource;

class ViewNotificationLog extends ViewRecord
{
    protected static string $resource = NotificationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Logs')
                ->url(fn () => NotificationLogResource::getUrl('index'))
                ->button(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Notification Details')
                    ->schema([
                        TextEntry::make('notification_type')
                            ->label('Notification Type')
                            ->formatStateUsing(fn($record) => ucwords(str_replace('_', ' ', $record->notification_type))),

                        TextEntry::make('mailable_class')
                            ->label('Mailable Class'),

                        TextEntry::make('recipient_email')
                            ->label('Recipient Email'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Sent' => 'success',
                                'Failed' => 'danger',
                            })
                            ->getStateUsing(fn($record) => ucfirst($record->status)),

                        TextEntry::make('sent_at')
                            ->label('Sent At'),
                    ])
                    ->columns(5),
                Section::make('Error Log')
                    ->schema([
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->columnSpan(2),

                        TextEntry::make('data')
                            ->label('Data Payload')
                            ->columnSpan(3)
                            ->getStateUsing(function ($record) {
                                $data = $record->data;

                                // Convert the data to array/object if it's a JSON string
                                if (is_string($data)) {
                                    $decoded = json_decode($data, true);
                                    return $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $data;
                                }

                                // If it's already an array/object, format it directly
                                return $data ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}';
                            }),
                    ])
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Format the data field for display
        if (!empty($record->data)) {
            $data['formatted_data'] = json_encode($record->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            $data['formatted_data'] = '{}';
        }

        return $data;
    }
}