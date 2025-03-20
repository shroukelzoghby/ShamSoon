<?php

namespace App\Filament\Resources\SolarPanelResource\Pages;

use App\Filament\Resources\SolarPanelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolarPanels extends ListRecords
{
    protected static string $resource = SolarPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
