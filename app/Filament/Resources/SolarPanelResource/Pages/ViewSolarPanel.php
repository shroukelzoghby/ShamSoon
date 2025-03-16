<?php

namespace App\Filament\Resources\SolarPanelResource\Pages;

use App\Filament\Resources\SolarPanelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSolarPanel extends ViewRecord
{
    protected static string $resource = SolarPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
