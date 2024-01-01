<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Harishdurga\LaravelQuiz\Models\QuizAttempt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QuizPerformance extends ChartWidget
{
    protected static ?string $heading = 'Quiz performance';
    protected int | string | array $columnSpan = 3;

    protected function getData(): array
    {
        $data = Trend::query(
                    QuizAttempt::where('participant_id', Cache::get('ward'))
                )
                ->between(
                    start: now()->subYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count('score');

        return [
            'datasets' => [
                [
                    'label' => 'Quiz performance',
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

    // protected function getFilters(): ?array
    // {
    //     return [
    //         'today' => 'Today',
    //         'week' => 'Last week',
    //         'month' => 'Last month',
    //         'year' => 'This year',
    //     ];
    // }
}
