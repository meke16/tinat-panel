<?php

namespace App\Filament\Resources\Videos;

use App\Filament\Resources\Videos\Pages\ManageVideos;
use App\Models\Video;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Chapter;
use App\Services\YouTubeMetadataService;
use Dom\Text;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Google\Service\Slides\Placeholder;

class VideosResource extends Resource
{
    protected static ?string $model = Video::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Grade, Subject, Chapter
            Select::make('grade_id')
                ->label('Grade')
                ->options(fn() => Grade::pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('subject_id', null))
                ->required(),

            Select::make('subject_id')
                ->label('Subject')
                ->options(fn($get) => Subject::where('grade_id', $get('grade_id'))->pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('chapter_id', null))
                ->required(),

            Select::make('chapter_id')
                ->label('Chapter')
                ->options(fn($get) => Chapter::where('subject_id', $get('subject_id'))->pluck('name', 'id'))
                ->required(),

            // Video Details
            TextInput::make('url')
                ->label('YouTube URL')
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if (!$state) return;

                    try {
                        $service = new \App\Services\YouTubeMetadataService();
                        $info = $service->getVideoInfo($state);

                        if ($info) {
                            $set('title', $info['title'] ?? '');
                            $set('author', $info['author'] ?? '');
                            $set('description', $info['description'] ?? '');

                            // Convert seconds to minutes
                            $set('duration', round($info['duration'] / 60, 2));

                            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $state, $matches);
                            $videoId = $matches[1] ?? null;

                            $embed = $videoId
                                ? '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>'
                                : null;

                            $set('url', $embed ?? $state);
                        }
                    } catch (\Exception $e) {
                        // clear fields on error
                        $set('title', '');
                        $set('author', '');
                        $set('description', '');
                        $set('duration', '');
                        $set('url', '');
                    }
                })
                ->placeholder('Enter YouTube video URL')
                ->required()
                ->columnSpanFull(),

            TextInput::make('title')
                ->placeholder('Video Title')
                ->label('Title')
                ->required(),

            TextInput::make('author')
                ->placeholder('Video Author')
                ->label('Author'),

            TextInput::make('type')
                ->default('Video')
                ->placeholder('e.g., Video, Tutorial')
                ->label('Type'),

            TextInput::make('duration')
                ->label('Duration')
                ->placeholder('e.g., 5:30 for 5 minutes 30 seconds'),

            TextInput::make('size')
                ->placeholder('e.g., 50MB, 1.5GB')
                ->label('Size (optional)'),

            Textarea::make('description')
                ->placeholder('Video Description')
                ->label('Description')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->limit(30)
                    ->colors(['info'])
                    ->sortable()
                    ->searchable(),

                TextColumn::make('author')
                    ->label('Author')
                    ->limit(20)
                    ->colors(['info'])
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->colors(['info'])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Size')
                    ->sortable()
                    ->colors(['info'])
                    ->searchable(),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->colors(['info'])
                    ->sortable(),

                BadgeColumn::make('grade.name')
                    ->label('Grade')
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('subject.name')
                    ->label('Subject')
                    ->color('success')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('chapter.name')
                    ->label('Chapter')
                    ->color('warning')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->relationship('grade', 'name')
                    ->label('Grade'),

                SelectFilter::make('subject')
                    ->relationship('subject', 'name')
                    ->label('Subject'),

                SelectFilter::make('chapter')
                    ->relationship('chapter', 'name')
                    ->label('Chapter'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageVideos::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manage Materials';
    }

    public static function getNavigationLabel(): string
    {
        return 'Videos';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-video-camera';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }
}
