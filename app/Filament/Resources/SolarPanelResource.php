<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SolarPanel;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SolarPanelResource\Pages;
use App\Filament\Resources\SolarPanelResource\RelationManagers;

class SolarPanelResource extends Resource
{
    protected static ?string $model = SolarPanel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'user.name';
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->user->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Performance' => $record->performance . '%',
            'Produced' => $record->energy_produced . ' kWh',
            'Consumed' => $record->energy_consumed . ' kWh',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('performance')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('energy_produced')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('energy_consumed')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('performance')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('energy_produced')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('energy_consumed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('performance')
                ->form([
                    TextInput::make('min')->label('Min Performance'),
                    TextInput::make('max')->label('Max Performance'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['min'], fn ($query, $min) => $query->where('performance', '>=', $min))
                        ->when($data['max'], fn ($query, $max) => $query->where('performance', '<=', $max));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (!empty($data['min'])) {
                        $indicators[] = 'Performance ≥ ' . $data['min'];
                    }

                    if (!empty($data['max'])) {
                        $indicators[] = 'Performance ≤ ' . $data['max'];
                    }

                    return $indicators;
                }),

                Filter::make('energy_produced')
                ->form([
                    TextInput::make('min')->label('Min Energy Produced'),
                    TextInput::make('max')->label('Max Energy Produced'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['min'], fn ($query, $min) => $query->where('energy_produced', '>=', $min))
                        ->when($data['max'], fn ($query, $max) => $query->where('energy_produced', '<=', $max));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (!empty($data['min'])) {
                        $indicators[] = 'Energy Produced ≥ ' . $data['min'];
                    }

                    if (!empty($data['max'])) {
                        $indicators[] = 'Energy Produced ≤ ' . $data['max'];
                    }

                    return $indicators;
                }),

                Filter::make('energy_consumed')
                ->form([
                    TextInput::make('min')->label('Min Energy Consumed'),
                    TextInput::make('max')->label('Max Energy Consumed'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['min'], fn ($query, $min) => $query->where('energy_consumed', '>=', $min))
                        ->when($data['max'], fn ($query, $max) => $query->where('energy_consumed', '<=', $max));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (!empty($data['min'])) {
                        $indicators[] = 'Energy Consumed ≥ ' . $data['min'];
                    }

                    if (!empty($data['max'])) {
                        $indicators[] = 'Energy Consumed ≤ ' . $data['max'];
                    }

                    return $indicators;
                }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );

                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString());

                        }

                        if ($data['until'] ?? null) {
                            $indicators['created_until'] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString());

                        }

                        return $indicators;
                    })->columns(2)->columnSpanFull()
            ])->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Solar Panel info')

                    ->schema([
                        TextEntry::make('user_id'),
                        TextEntry::make('performance'),
                        TextEntry::make('energy_produced'),
                        TextEntry::make('energy_consumed'),
                    ])->columns(2)
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
            'index' => Pages\ListSolarPanels::route('/'),
            'create' => Pages\CreateSolarPanel::route('/create'),
            'edit' => Pages\EditSolarPanel::route('/{record}/edit'),
        ];
    }
}
