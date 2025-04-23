<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class UserRegisterationsChart extends ChartWidget
{
    protected static ?string $heading = 'Users';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $start = now()->startOfYear(); 
    $end = now()->endOfYear();

    $data = Trend::model(User::class)
        ->between(start: $start, end: $end)
        ->perMonth()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'User Registrations',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];

    }
    protected function getType(): string
    {
        return 'line';
    }
}
