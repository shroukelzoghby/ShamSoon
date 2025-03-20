<?php

namespace App\Filament\Resources\SolarPanelResource\Pages;

use App\Filament\Resources\SolarPanelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolarPanel extends EditRecord
{
    protected static string $resource = SolarPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
