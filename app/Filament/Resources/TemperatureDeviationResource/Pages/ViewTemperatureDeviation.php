<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Models\TemperatureDeviation;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TemperatureDeviationResource;

class ViewTemperatureDeviation extends ViewRecord
{
    protected static string $resource = TemperatureDeviationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => Auth::user()->hasRole(['Supply Chain Officer', 'QA Staff'])),
            Action::make('is_reviewed')
                ->label('Mark as Reviewed')
                ->visible(function (TemperatureDeviation $record) {
                    $isAcknowledged = $record->is_acknowledged == false && $record->length_temperature_deviation != null && $record->risk_analysis != null;
                    $admin = Auth::user()->hasRole('Supply Chain Manager');
                    return $isAcknowledged && $admin;
                })
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
                    ->visible(function (TemperatureDeviation $record) {
                        $isAcknowledged = $record->is_acknowledged == false && $record->length_temperature_deviation != null && $record->risk_analysis != null;
                        $admin = Auth::user()->hasRole('QA Manager');
                        return $isAcknowledged && $admin;
                    })
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
}
