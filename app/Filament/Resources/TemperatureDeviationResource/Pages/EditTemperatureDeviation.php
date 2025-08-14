<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TemperatureDeviationResource;
use App\Traits\HasLocationBasedAccess;

class EditTemperatureDeviation extends EditRecord
{
    use HasLocationBasedAccess;
    
    protected static string $resource = TemperatureDeviationResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Check if user can access this record's location
        if (!static::canAccessLocation($this->record->location_id)) {
            abort(403, 'You do not have permission to access this record.');
        }
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        auth()->user()->hasRole('Security') 
            ? $data['pic'] = auth()->user()->name 
            : $data['pic'] = auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));
        $data['analyzer_pic'] = auth()->user()->initial.' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));
        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('is_reviewed')
                ->label('Mark as Reviewed')
                ->visible(fn () => Auth::user()->hasRole('Supply Chain Manager'))
                ->action(function (Model $record) {
                    $record->update([
                        'is_reviewed' => true,
                        'reviewed_by' => auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y')),
                        'reviewed_at' => now('Asia/Jakarta'),
                    ]);
                Notification::make()
                    ->title('Success!')
                    ->body('Marked as reviewed successfully by Supply Chain Manager.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check'),
            Action::make('is_acknowledged')
                ->label('Mark as Acknowledged')
                ->visible(fn () => Auth::user()->hasRole('QA Manager'))
                ->action(function (Model $record) {
                    $record->update([
                        'is_acknowledged' => true,
                        'acknowledged_by' => auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y')),
                        'acknowledged_at' => now('Asia/Jakarta'),
                    ]);
                Notification::make()
                    ->title('Success!')
                    ->body('Marked as acknowledged successfully by QA Manager.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->color('info')
                ->icon('heroicon-o-check'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // Default fallback
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Temperature Deviation successfully updated');
    }
}
