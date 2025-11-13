<?php

namespace App\Filament\Widgets;

use App\Models\Question;
use App\Models\Material;
use App\Models\Video;
use App\Models\VideoMaterial;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyGrowthChart extends ChartWidget
{
 protected static ?int $sort = 4;
 protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('Monthly Content Growth');
    }   

    protected function getData(): array
    {
        // Get current year
        $currentYear = now()->year;

        // Questions per month
        $questionsData = Question::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Materials per month
        $materialsData = Material::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Videos per month
        $videosData = Video::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months with 0
        $months = range(1, 12);
        $questions = [];
        $materials = [];
        $videos = [];

        foreach ($months as $month) {
            $questions[] = $questionsData[$month] ?? 0;
            $materials[] = $materialsData[$month] ?? 0;
            $videos[] = $videosData[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Questions',
                    'data' => $questions,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Materials',
                    'data' => $materials,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Videos',
                    'data' => $videos,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}