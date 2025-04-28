<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Models\Role;
use Carbon\Carbon;
use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Query\Builder;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

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
                ->badge(Role::query()->where('created_at','>=', Carbon::now()->subWeek())->count()),
            'This month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->submonth()))
                ->badge(Role::query()->where('created_at','>=', Carbon::now()->subMonthsWithNoOverflow())->count()),
            'This year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->subyear()))
                ->badge(Role::query()->where('created_at','>=', Carbon::now()->subyear())->count()),
        ];
    }
}
