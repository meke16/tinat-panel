<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Subchapter;
use App\Models\Question;
use App\Models\Material;
use App\Models\Video;
use App\Models\VideoMaterial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{

    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Grades', Grade::count())
                ->description('Number of grade levels')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Subjects', Subject::count())
                ->description('Subjects across all grades')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('success')
                ->chart([2, 10, 5, 15, 8, 12, 10]),

            Stat::make('Total Chapters', Chapter::count())
                ->description('Chapters in all subjects')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning')
                ->chart([5, 8, 12, 8, 15, 10, 12]),

            Stat::make('Total Questions', Question::count())
                ->description('Questions in database')
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->color('info')
                ->chart([10, 15, 12, 18, 20, 15, 22]),

            Stat::make('Study Materials', Material::count())
                ->description('Text-based materials')
                ->descriptionIcon('heroicon-o-document')
                ->color('gray')
                ->chart([3, 8, 5, 10, 7, 12, 15]),

            Stat::make('Video Materials', Video::count())
                ->description('Video resources')
                ->descriptionIcon('heroicon-o-play')
                ->color('danger')
                ->chart([2, 5, 8, 6, 10, 12, 15]),
        ];
    }
}