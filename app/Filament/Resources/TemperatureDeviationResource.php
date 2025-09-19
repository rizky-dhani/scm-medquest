<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\SerialNumber;
use App\Models\RoomTemperature;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\TemperatureHumidity;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use App\Models\TemperatureDeviation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\TemperatureDeviationExport;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Resources\TemperatureDeviationResource\Pages;
use App\Traits\HasLocationBasedAccess;

class TemperatureDeviationResource extends Resource
{
    use HasLocationBasedAccess;
    
    protected static ?string $model = TemperatureDeviation::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Temperature Deviation';
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return static::applyLocationFilter(parent::getEloquentQuery());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('temperature_humidity_id')
                    ->default(request()->get('temp_id')),
                Section::make('Date & Time')
                ->columns(2)
                ->schema([
                    DatePicker::make('date')
                        ->label('Date')
                        ->default(Carbon::now())
                        ->required(),
                    TimePicker::make('time')
                        ->label('Time')
                        ->seconds(false)
                        ->default(function () {
                            $time = request()->get('time');
                            return $time ? Carbon::createFromFormat('H:i', $time)->format('H:i') : null;
                        })
                        ->required(),
                ]),
                Section::make('Location & Storage Temperature Standards')
                    ->columns(4)
                    ->schema([
                        Hidden::make('temperature_humidity_id')
                        ->default(function () {
                            $humidity = TemperatureHumidity::query()
                                ->whereDate('created_at', now('Asia/Jakarta')->toDateString())
                                ->latest()
                                ->first();

                            return $humidity->id;
                        }),                        
                        Select::make('location_id')
                        ->label('Location')
                        ->relationship(
                            'location', 
                            'location_name',
                            modifyQueryUsing: function (Builder $query) {
                                $accessibleLocationIds = static::getAccessibleLocationIds();
                                if (!empty($accessibleLocationIds)) {
                                    $query->whereIn('id', $accessibleLocationIds);
                                }
                            }
                        )
                        ->default(function () {
                            $user = Auth::user();
                            // Check URL parameter first
                            if (request()->get('location_id')) {
                                return request()->get('location_id');
                            }
                            // If user has specific location and doesn't have admin-level roles, default to that
                            if ($user->location_id && !$user->hasRole(['Super Admin', 'Admin', 'Supply Chain Manager', 'QA Manager'])) {
                                return $user->location_id;
                            }
                            return null;
                        })
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->required(),
                    Select::make('room_id')
                        ->label('Room')
                        ->relationship('room', 'room_name')
                        ->default(fn() => request()->get('room_id')??null)
                        ->options(function (callable $get) {
                            $locationId = $get('location_id');

                            if (!$locationId) {
                                return [];
                            }

                            return Room::where('location_id', $locationId)
                                ->pluck('room_name', 'id');
                        })
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->required()
                        ->disabled(fn (callable $get) => ! $get('location_id')),
                    Select::make('serial_number_id')
                        ->label('Serial Number')
                        ->default(fn() => request()->get('serial_number_id')??null)
                        ->options(function (callable $get) {
                            $roomId = $get('room_id');

                            if (!$roomId) {
                                return [];
                            }

                            return SerialNumber::where('room_id', $roomId)
                                ->pluck('serial_number', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->disabled(fn (callable $get) => ! $get('room_id'))
                        ->preload()
                        ->required(),
                    Select::make('room_temperature_id')
                        ->label('Room Temperature Standards')
                        ->default(fn() => request()->get('room_temperature_id')??null)
                        ->relationship('roomTemperature', 'temperature_start')
                        ->options(function (callable $get) {
                            $roomId = $get('room_id');
                            if (!$roomId) {
                                return [];
                            }
                            return RoomTemperature::where('room_id', $roomId)
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    $label = "{$item->temperature_start}°C to {$item->temperature_end}°C";
                                    return [$item->id => $label];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->reactive()
                        ->preload()
                        ->required()
                        ->disabled(fn (callable $get) => ! $get('room_id'))
                        ->afterStateUpdated(function ($state, callable $set) {
                            $roomTemperature = RoomTemperature::find($state);
                            if ($roomTemperature) {
                                $set('temperature_start', $roomTemperature->temperature_start);
                                $set('temperature_end', $roomTemperature->temperature_end);
                            }
                        }),
                        Hidden::make('temperature_start'),
                        Hidden::make('temperature_end')
                    ]),
                Section::make('Temperature Deviation & Reason (Filled by Staff / Security) ')
                ->columns(2)
                ->schema([
                    TextInput::make('temperature_deviation')
                        ->label('Temperature deviation (°C)')
                        ->required(Auth::user()->hasRole(['Staff', 'Security']))
                        ->default(fn() => request()->get('temperature_deviation'))
                        ->dehydrated(),
                    TextArea::make('deviation_reason')
                        ->label('Reason for deviation')
                        ->required(Auth::user()->hasRole(['Staff', 'Security']))
                        ->dehydrated(),
                ])->disabled(fn() => !Auth::user()->hasRole(['Supply Chain Officer', 'Security'])),
                Section::make('Length of Temperature Deviation & Risk Analysis (Filled by QA Staff / Supervisor)')
                ->columns(2)
                ->schema([
                    TextInput::make('length_temperature_deviation')
                        ->label('Length Temperature deviation (Minutes/Hours)')
                        ->required(Auth::user()->hasRole(['QA Staff', 'QA Supervisor']))
                        ->dehydrated(),
                    TextArea::make('risk_analysis')
                        ->label('Risk Analysis')
                        ->required(Auth::user()->hasRole(['QA Staff', 'QA Supervisor']))
                        ->dehydrated(),
                ])->disabled(fn() => !Auth::user()->hasAnyRole(['QA Staff', 'QA Supervisor'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->header(view('filament.tables.top-bottom-pagination-tables', [
                'table' => $table,
            ]))
            ->modifyQueryUsing(function ($query) {
                return $query->orderByDesc('created_at');
            })
            ->columns([
                TextColumn::make('location.location_name')
                    ->label('Location')
                    ->getStateUsing(function ($record) {
                        return $record->location->location_name . '<br>' .
                        $record->room->room_name . '<br>' .
                        $record->serialNumber->serial_number;
                    })
                    ->sortable()
                    ->searchable()
                    ->html(),
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
                TextColumn::make('reviewed_by')
                    ->label('Reviewed by'),
                TextColumn::make('acknowledged_by')
                    ->label('Acknowledged by')
            ])
            ->filters([
                SelectFilter::make('temperature_humidity_id')
                    ->label('Temperature Humidity')
                    ->relationship('temperatureHumidity', 'id')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'location_name')
                    ->searchable()
                    ->preload(),
                Filter::make('period')
                    ->form([
                        DatePicker::make('period')
                            ->label('Period')
                            ->displayFormat('M Y')
                            ->native(false)
                            ->closeOnDateSelection()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['period']) {
                            return $query;
                        }
                        
                        $date = Carbon::parse($data['period']);
                        return $query->whereMonth('date', $date->month)
                                   ->whereYear('date', $date->year);
                    })
            ])
            ->headerActions([
                
                Action::make('custom_export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                Select::make('location_id')
                    ->label('Location')
                    ->options(Location::pluck('location_name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->required(),
                Select::make('room_id')
                ->label('Room')
                ->relationship('room', 'room_name')
                ->options(function (callable $get) {
                    $locationId = $get('location_id');

                    if (!$locationId) {
                        return [];
                    }

                    return Room::where('location_id', $locationId)
                        ->pluck('room_name', 'id');
                })
                ->searchable()
                ->reactive()
                ->preload()
                ->required()
                ->disabled(fn (callable $get) => ! $get('location_id'))
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('serial_number_id', null);
                }),

                Select::make('serial_number_id')
                    ->label('Serial Number')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');

                        if (!$roomId) {
                            return [];
                        }

                        return SerialNumber::where('room_id', $roomId)
                            ->pluck('serial_number', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn (callable $get) => ! $get('room_id'))
                    ->preload()
                    ->required(),
                Select::make('room_temperature_id')
                    ->label('Room Temperature Standards')
                    ->relationship('roomTemperature', 'temperature_start')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');
                        if (!$roomId) {
                            return [];
                        }
                        return RoomTemperature::where('room_id', $roomId)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                $label = "{$item->temperature_start}°C to {$item->temperature_end}°C";
                                return [$item->id => $label];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->required()
                    ->disabled(fn (callable $get) => ! $get('room_id')),
                Select::make('month_type')
                    ->label('Month Type')
                    ->options([
                        'this_month' => 'This Month',
                        'choose' => 'Choose Month',
                    ])
                    ->default('this_month')
                    ->reactive(),

                DatePicker::make('chosen_month')
                    ->label('Choose Month')
                    ->displayFormat('F Y')
                    ->visible(fn ($get) => $get('month_type') === 'choose')
                    ->required(fn ($get) => $get('month_type') === 'choose'),
            ])
            ->action(function (array $data) {
                $location_id = $data['location_id'];
                $room_id = $data['room_id'] ?? null;
                $serial_number_id = $data['serial_number_id'] ?? null;
                $room_temperature_id = $data['room_temperature_id'] ?? null;
                $location = Location::find($location_id);
                $room = Room::find($room_id);
                $serialNumber = SerialNumber::find($serial_number_id);
                if ($data['month_type'] === 'this_month') {
                    $month = now()->month;
                    $year = now()->year;
                } else {
                    $chosenMonth = Carbon::parse($data['chosen_month']);
                    $month = $chosenMonth->month;
                    $year = $chosenMonth->year;
                }
                $records = TemperatureDeviation::query()
                    ->where([
                        ['location_id', '=', $location_id],
                        ['room_id', '=', $room_id],
                        ['serial_number_id', '=', $serial_number_id],
                        ['room_temperature_id', '=', $room_temperature_id],
                    ])
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->with(['location', 'room', 'serialNumber', 'roomTemperature'])
                    ->get();

                $monthName = strtoupper(Carbon::createFromDate($year, $month)->format('M'));
                $sluggedLocation = strtoupper($location->location_name.'_'.$room->room_name.'_'.$serialNumber->serial_number);
                $filename = "TemperatureDeviation_{$monthName}{$year}_{$sluggedLocation}.xlsx";

                return Excel::download(new TemperatureDeviationExport($records), $filename);
            })
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                ->visible(fn($record) => $record->date == now()->toDateString() && Auth::user()->hasRole(['Supply Chain Officer', 'QA Staff'])),
                DeleteAction::make()
                ->visible(fn($record) => $record->date == now()->toDateString() && Auth::user()->hasRole(['Supply Chain Officer', 'QA Staff'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Date & Time')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('date')
                            ->label('Date')
                            ->date('d/m/Y'),
                        TextEntry::make('time')
                            ->label('Time')
                            ->time('H:i'),
                    ]),
                InfoSection::make('Reviewed & Acknowledged')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reviewed_by')
                            ->label('Reviewed By')
                            ->formatStateUsing(fn ($record) => $record->reviewed_by ? $record->reviewed_by : '-'),
                        TextEntry::make('acknowledged_by')
                            ->label('Acknowledged By')
                            ->formatStateUsing(fn ($record) => $record->acknowledged_by ? $record->acknowledged_by : '-'),
                    ]),
                InfoSection::make('Location & Storage Temperature Standards')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('location_id')
                            ->label('Location')
                            ->formatStateUsing(fn ($record) => $record->location->location_name),
                            TextEntry::make('room_id')
                                ->label('Room')
                                ->formatStateUsing(fn ($record) => $record->room->room_name),
                            TextEntry::make('serial_number_id')
                                ->label('Serial Number')
                                ->formatStateUsing(fn ($record) => $record->serialNumber->serial_number),
                        TextEntry::make('room_temperature_id')
                            ->label('Storage Temperature Standards')
                            ->formatStateUsing(fn ($record) => $record->roomTemperature->temperature_start.'°C to '.$record->roomTemperature->temperature_end.'°C'),
                    ]),
                InfoSection::make('Temperature Deviation & Reason')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('temperature_deviation')
                            ->label('Temperature Deviation'),
                        TextEntry::make('deviation_reason')
                            ->label('Reason for Deviation'),
                    ]),
                InfoSection::make('Length of Temperature Deviation & Risk Analysis')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('length_temperature_deviation')
                            ->label('Length of Temperature Deviation'),
                        TextEntry::make('risk_analysis')
                            ->label('Risk Analysis'),
                    ]),
            ]);
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
            'index' => Pages\ListTemperatureDeviations::route('/'),
            'create' => Pages\CreateTemperatureDeviation::route('/create'),
            'edit' => Pages\EditTemperatureDeviation::route('/{record}/edit'),
            'view' => Pages\ViewTemperatureDeviation::route('/view/{record}'),
            'reviewed' => Pages\ReviewedTemperatureDeviation::route('/reviewed'),
            'acknowledged' => Pages\AcknowledgedTemperatureDeviation::route('/acknowledged'),
        ];
    }
}
