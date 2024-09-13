<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class AveragePassesChart extends ChartWidget
{
    protected static ?string $maxHeight = '200px';
    protected static ?string $heading = 'Average passes per subject';

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
                        'rgb(255, 99, 132)', // Red
                        'rgb(54, 162, 235)', // Blue
                        'rgb(255, 205, 86)', // Yellow
                        'rgb(75, 192, 192)', // Green
                        'rgb(153, 102, 255)', // Purple
                        'rgb(255, 159, 64)', // Orange
                        'rgb(0, 0, 0)', // Black
                        'rgb(255, 0, 255)', // Magenta
                        'rgb(0, 255, 255)', // Cyan
                        'rgb(128, 128, 128)', // Gray
                    ],
                ],
            ],
            'labels' => self::getSubjects(),
        ];
    }

    protected static function getSubjects()
    {
        if (Auth::user()->isHighSchool()) {
            return Subject::with('level')->whereHas('level', function ($query) {
                $query->where('order', '>=', 12);
            })->pluck('code')->toArray();
        } else {
            return Subject::with('level')->whereHas('level', function ($query) {
                $query->where('order', '<', 12);
            })->pluck('code')->toArray();
        }
    }

    protected static function getAverageScoresPerClass()
    {
        $averageScores = Result::with('subject')
                            ->select('subject_id', DB::raw('AVG(total_score) as average_score'))
                            ->groupBy('subject_id')
                            ->get()
                            ->mapWithKeys(function ($result) {
                                return [$result->subject->code => round($result->average_score, 2)];  // Replace 'name' with the class attribute you want
                            })
                            ->toArray();

        return $averageScores;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
