<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use App\Models\Subject;
use Filament\Widgets\ChartWidget;

class SubjectsPerGradeChart extends ChartWidget
{
     protected static ?int $sort = 2;


public function getHeading(): string
{
    return __('Subjects per Grade');
}
    protected function getData(): array
    {
        $grades = Grade::withCount('subjects')->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Subjects Count',
                    'data' => $grades->pluck('subjects_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ],
                    'borderColor' => '#ffffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $grades->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}