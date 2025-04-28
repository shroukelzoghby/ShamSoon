<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Feedback;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class CommunityChart extends ChartWidget
{
    protected static ?string $heading = 'Community';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $start = now()->startOfYear(); // last 6 weeks
    $end = now()->endOfYear();

    $postData = Trend::model(Post::class)->between($start, $end)->perMonth()->count();
    $commentData = Trend::model(Comment::class)->between($start, $end)->perMonth()->count();
    $feedbackData = Trend::model(Feedback::class)->between($start, $end)->perMonth()->count();

    return [
        'datasets' => [
            [
                'label' => 'Posts',
                'data' => $postData->map(fn (TrendValue $value) => $value->aggregate),
            ],
            [
                'label' => 'Comments',
                'data' => $commentData->map(fn (TrendValue $value) => $value->aggregate),
            ],
            [
                'label' => 'Feedbacks',
                'data' => $feedbackData->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $postData->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
