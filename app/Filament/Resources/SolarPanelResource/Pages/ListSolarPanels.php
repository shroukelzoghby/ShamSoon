<?php

namespace App\Filament\Resources\SolarPanelResource\Pages;

use App\Models\SolarPanel;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\SolarPanelResource;
use Illuminate\Contracts\Database\Query\Builder;

class ListSolarPanels extends ListRecords
{
    protected static string $resource = SolarPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'This week' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', Carbon::now()->subWeek()))
                ->badge(SolarPanel::query()->where('created_at','>=', Carbon::now()->subWeek())->count()),
            'This month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->submonth()))
                ->badge(SolarPanel::query()->where('created_at','>=', Carbon::now()->submonth())->count()),
            'This year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->subyear()))
                ->badge(SolarPanel::query()->where('created_at','>=', Carbon::now()->subyear())->count()),
        ];
    }
}
