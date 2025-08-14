<?php

namespace App\Filament\Resources\TemperatureDeviationResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Models\TemperatureDeviation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\TemperatureDeviationResource;

class ReviewedTemperatureDeviation extends ListRecords
{
    protected static string $resource = TemperatureDeviationResource::class;
    protected static ?string $title = 'Pending Review';
    public function getBreadcrumb(): string
    {
        return 'Pending Review'; // or any label you want
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('date')->where('is_reviewed', false)->whereNotNull('length_temperature_deviation')->whereNotNull('risk_analysis'))
            ->emptyStateHeading('No pending review data is found')
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
            ->actions([
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('is_reviewed')
                    ->label('Mark as Reviewed')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function (TemperatureDeviation $record) {
                        $isAcknowledged = $record->is_acknowledged == false && $record->length_temperature_deviation != null && $record->risk_analysis != null;
                        $admin = Auth::user()->hasRole('Supply Chain Manager');
                        return $isAcknowledged && $admin;
                    })
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->is_reviewed = true;
                            $record->reviewed_by = auth()->user()->initial . ' ' . strtoupper(now('Asia/Jakarta')->format('d M Y'));
                            $record->reviewed_at = now('Asia/Jakarta');
                            $record->save();
                        }
                        
                    Notification::make()
                        ->title('Success!')
                        ->body('Selected data marked as reviewed successfully')
                        ->success()
                        ->send();
                    }),
                ]),
            ]);
    }
}
