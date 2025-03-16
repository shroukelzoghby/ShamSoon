<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SolarPanelResource\Pages;
use App\Filament\Resources\SolarPanelResource\RelationManagers;
use App\Models\SolarPanel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SolarPanelResource extends Resource
{
    protected static ?string $model = SolarPanel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('performance')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
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
            'view' => Pages\ViewSolarPanel::route('/{record}'),
            'edit' => Pages\EditSolarPanel::route('/{record}/edit'),
        ];
    }
}
