<?php

namespace App\Filament\Widgets;

use App\Models\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class CarbonChart extends ChartWidget
{
    protected static ?string $heading = 'Carbon';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $start = now()->startOfYear(); 
        $end = now()->endOfYear();
    
        $data = Trend::model(Carbon::class)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();
    
        return [
            'datasets' => [
                [
                    'label' => 'Co2 Saved',
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
