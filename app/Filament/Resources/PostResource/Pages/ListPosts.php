<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Actions;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Query\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

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
                ->badge(Post::query()->where('created_at','>=', Carbon::now()->subWeek())->count()),
            'This month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->submonth()))
                ->badge(post::query()->where('created_at','>=', Carbon::now()->submonth())->count()),
            'This year' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at','>=', carbon::now()->subyear()))
                ->badge(post::query()->where('created_at','>=', Carbon::now()->subyear())->count()),
        ];
    }
}
