<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\User;
use App\Models\Carbon;
use App\Models\Comment;
use App\Models\Feedback;
use App\Models\SolarPanel;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAdminOverview extends BaseWidget
{
    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Registered users')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Solar Panels Purchased', SolarPanel::count())
                ->description('Panels deployed')
                ->icon('heroicon-o-sun')
                ->color('success'),
            Stat::make('CO₂ Emission Saved', Carbon::sum('Co2_saved') . ' kw/h')
                ->description('Environmental impact')
                ->icon('heroicon-o-cloud')
                ->color('success'),

            Stat::make('Trees', Carbon::sum('equivalent_trees'))
                ->description('Equivalent Trees planted')
                ->icon('heroicon-o-sparkles')
                ->color('success'),
            Stat::make('Total Energy Generated', SolarPanel::sum('energy_produced') . ' kw/h')
                ->description('Total energy output')
                ->icon('heroicon-o-bolt')
                ->color('success'),
            Stat::make('Posts', Post::count())
                ->description('User Posts in the Community')
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Comments', Comment::count())
                ->description('User Comments on Posts')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success'),
            Stat::make('Feedbacks', Feedback::count())
                ->description('Feedback Messages Received')
                ->icon('heroicon-o-megaphone')
                ->color('success'),
        ];
    }
    protected function getColumns(): int
    {
        return 4;
    }
}
