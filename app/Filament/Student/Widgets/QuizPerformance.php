<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Harishdurga\LaravelQuiz\Models\QuizAttempt;
use Illuminate\Support\Facades\Log;

class QuizPerformance extends ChartWidget
{
    protected static ?string $heading = 'Quiz performance';

    protected function getData(): array
    {
        $data = Trend::query(
                    QuizAttempt::where('participant_id', auth()->user()->id)
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
                    'label' => 'Quiz performance this month',
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
