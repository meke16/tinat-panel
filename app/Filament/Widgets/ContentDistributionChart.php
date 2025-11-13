<?php

namespace App\Filament\Widgets;

use App\Models\Chapter;
use App\Models\Subchapter;
use App\Models\Question;
use App\Models\Material;
use App\Models\Video;
use App\Models\VideoMaterial;
use Filament\Widgets\ChartWidget;

class ContentDistributionChart extends ChartWidget
{
     protected static ?int $sort = 3;
public function getHeading(): string
{
    return __('Content Distribution');
}

    protected function getData(): array
    {
        $data = [
            Chapter::count(),
            Subchapter::count(),
            Question::count(),
            Material::count(),
            Video::count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Content Count',
                    'data' => $data,
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                    ],
                ],
            ],
            'labels' => ['Chapters', 'Subchapters', 'Questions', 'Materials', 'Videos'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
}