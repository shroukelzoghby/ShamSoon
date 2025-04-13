<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SolarPanel;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SolarPanelResource\Pages;
use App\Filament\Resources\SolarPanelResource\RelationManagers;

class SolarPanelResource extends Resource
{
    protected static ?string $model = SolarPanel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
