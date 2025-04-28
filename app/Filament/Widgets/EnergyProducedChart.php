<?php

namespace App\Filament\Widgets;

use App\Models\SolarPanel;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class EnergyProducedChart extends ChartWidget
{
    protected static ?string $heading = 'Energy';
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $data = Trend::model(SolarPanel::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('energy_produced');

    return [
        'datasets' => [
            [
                'label' => 'Energy Generated',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    
        
    
    
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
