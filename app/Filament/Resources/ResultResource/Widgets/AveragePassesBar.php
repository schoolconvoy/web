<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use App\Models\Level;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class AveragePassesBar extends ChartWidget
{
    protected static ?string $heading = 'Average passes per class';
    protected static ?string $maxHeight = '200px';
    protected static ?string $minHeight = '200px';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => self::getAverageScoresPerClass(),
                    'backgroundColor' => [
                        'rgb(255, 0, 0)',   // Red
                        'rgb(0, 255, 0)',   // Green
                        'rgb(0, 0, 255)',   // Blue
                        'rgb(255, 255, 0)', // Yellow
                        'rgb(255, 0, 255)', // Magenta
                        'rgb(0, 255, 255)', // Cyan
                        'rgb(128, 0, 0)',   // Maroon
                        'rgb(0, 128, 0)',   // Green (dark)
                        'rgb(0, 0, 128)',   // Navy
                        'rgb(128, 128, 0)', // Olive
                        'rgb(128, 0, 128)', // Purple
                    ]
                ],
            ],
            'labels' => self::getLevels(),
        ];
    }

    protected static function getLevels()
    {
        if (Auth::user()->isHighSchool()) {
            return Level::where('order', '>=', 12)->pluck('shortname')->toArray();
        } else {
            return Level::where('order', '<', 12)->pluck('shortname')->toArray();
        }
    }

    protected static function getAverageScoresPerClass()
    {
        $averageScores = Result::with('class')
                            ->select('class_id', DB::raw('AVG(total_score) as average_score'))
                            ->groupBy('class_id')
                            ->get()
                            ->mapWithKeys(function ($result) {
                                return [$result->class->level->shortname => round($result->average_score, 2)];  // Replace 'name' with the class attribute you want
                            })
                            ->toArray();

        return $averageScores;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
