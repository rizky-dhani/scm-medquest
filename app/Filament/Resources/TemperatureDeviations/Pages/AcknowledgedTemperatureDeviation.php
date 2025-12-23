<?php

namespace App\Filament\Resources\TemperatureDeviations\Pages;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions;
use Filament\Tables\Table;
use App\Models\TemperatureDeviation;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\listRecords;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\TemperatureDeviations\TemperatureDeviationResource;

class AcknowledgedTemperatureDeviation extends listRecords
{
    protected static string $resource = TemperatureDeviationResource::class;
    protected static ?string $title = 'Pending Acknowledgement';
    public function getBreadcrumb(): string
    {
        return 'Pending Acknowledgement'; // or any label you want
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->header(view('filament.tables.top-bottom-pagination-tables', [
                'table' => $table,
            ]))
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('date')->where('is_acknowledged', false)->whereNotNull('length_temperature_deviation')->whereNotNull('risk_analysis'))
            ->emptyStateHeading('No pending acknowledge data is found')
            ->columns([
                TextColumn::make('date')
                    ->label('Date (Tanggal)')
                    ->sortable()
                    ->searchable()
                    ->date('d/m/Y'),
                TextColumn::make('time')
                    ->label('Time (Jam)')
                    ->sortable()
                    ->searchable()
                    ->time('H:i'),
                TextColumn::make('temperature_deviation')
                    ->label('Temperature Deviation (°C)'),
                TextColumn::make('length_temperature_deviation')
                    ->label('Length of Temperature Deviation (Menit/Jam)'),
                TextColumn::make('deviation_reason')
                    ->label('Reason for Deviation'),
                TextColumn::make('pic')
                    ->label('PIC (SCM)'),
                TextColumn::make('risk_analysis')
                    ->label('Risk Analysis of impact deviation'),
                TextColumn::make('analyzer_pic')
                    ->label('Analyzed by (QA)'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('is_acknowledged')
                    ->label('Mark as Acknowledged')
                    ->visible(function () {
                        $admin = Auth::user()->hasRole('QA Manager');
                        return $admin;
                    })
                    ->action(function (Model $record) {
                        $record->update([
                            'is_acknowledged' => true,
                            'acknowledged_by' => auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y')),
                            'acknowledged_at' => now('Asia/Jakarta'),
                        ]);
                    Notification::make()
                        ->title('Success!')
                        ->body('Marked as acknowledged successfully')
                        ->success()
                        ->send();
                    })
                    ->requiresConfirmation()
                    ->color('info')
                    ->icon('heroicon-o-check'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                BulkAction::make('is_acknowledged')
                    ->label('Mark as Acknowledged')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function () {
                        return Auth::user()->hasRole('QA Manager');
                    })
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->is_acknowledged = true;
                            $record->acknowledged_by = auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));
                            $record->acknowledged_at = now('Asia/Jakarta');
                            $record->save();
                        }
                        
                        Notification::make()
                            ->title('Success!')
                            ->body('Selected data marked as acknowledged successfully')
                            ->success()
                            ->send();
                    }),
                ]),
            ]);
    }
}
