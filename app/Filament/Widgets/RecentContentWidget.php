<?php

namespace App\Filament\Widgets;

use App\Models\Question;
use App\Models\Material;
use App\Models\Video;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class RecentContentWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 5;

    protected function getTableData(): Collection
    {
        $recentQuestions = Question::with(['subchapter.chapter.subject'])
            ->latest()
            ->take(5)
            ->get()
           
            ->map(fn($question) => [
                'id' => $question->id,
                'type' => 'Question',
                'title' => str($question->subchapter->chapter->name)->limit(60),
                'subject' => $question->subchapter->chapter->subject->name ?? 'N/A',
                'created_at' => $question->created_at,
            ]);
            

        $recentMaterials = Material::with(['chapter.subject'])
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($material) => [
                'id' => $material->id,
                'type' => 'Material',
                'title' => str($material->title)->limit(60),
                'subject' => $material->chapter->subject->name ?? 'N/A',
                'created_at' => $material->created_at,
            ]);

        $recentVideos = Video::with(['chapter.subject'])
            ->latest()
            ->take(2)
            ->get()
            ->map(fn($video) => [
                'id' => $video->id,
                'type' => 'Video',
                'title' => str($video->title)->limit(60),
                'subject' => $video->chapter->subject->name ?? 'N/A',
                'created_at' => $video->created_at,
            ]);

        return collect()
            ->merge($recentQuestions)
            ->merge($recentMaterials)
            ->merge($recentVideos)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('📚 Recent Content Activity')
            ->description('Latest Questions, Materials, and Videos added recently.')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Question' => 'primary',
                        'Material' => 'success',
                        'Video' => 'danger',
                        default => 'gray',
                    })
                    ->label('Type'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Content Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->badge()
                    ->color('gray')
                    ->label('Subject'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->records(fn() => $this->getTableData()->toArray()); // ✅ convert to array
    }
}
