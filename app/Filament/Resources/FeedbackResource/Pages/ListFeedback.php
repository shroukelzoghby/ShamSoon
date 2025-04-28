<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Models\Feedback;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\FeedbackResource;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Query\Builder;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

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
                ->badge(Feedback::query()->where('created_at','>=', Carbon::now()->subWeek())->count()),
            'This month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->submonth()))
                ->badge(Feedback::query()->where('created_at','>=', Carbon::now()->submonth())->count()),
            'This year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->subyear()))
                ->badge(Feedback::query()->where('created_at','>=', Carbon::now()->subyear())->count()),
        ];
    }
}
