<?php

namespace App\Filament\Resources\Subchapters;

use App\Filament\Resources\Subchapters\Pages\ManageSubchapters;
use App\Models\Chapter;
use App\Models\Grade;
use App\Models\Subchapter;
use App\Models\Subject;
use BackedEnum;
use Doctrine\DBAL\Schema\View;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubchapterResource extends Resource
{
    protected static ?string $model = Subchapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        // 1️⃣ Grade
        Select::make('grade_id')
            ->label('Grade')
            ->options(Grade::pluck('name', 'id'))
            ->reactive()
            ->afterStateHydrated(function ($set, $record) {
                if ($record && $record->chapter?->subject?->grade_id) {
                    $set('grade_id', $record->chapter->subject->grade_id);
                }
            })
            ->afterStateUpdated(fn ($set) => $set('subject_id', null))
            ->required(),
            // ->disabled(fn ($record) => filled($record))  --disable when editing

        // 2️⃣ Subject
        Select::make('subject_id')
            ->label('Subject')
            ->options(function (callable $get) {
                $gradeId = $get('grade_id');
                if (!$gradeId) return Subject::pluck('name', 'id');
                return Subject::where('grade_id', $gradeId)->pluck('name', 'id');
            })
            ->reactive()
            ->afterStateHydrated(function ($set, $record) {
                if ($record && $record->chapter?->subject_id) {
                    $set('subject_id', $record->chapter->subject_id);
                }
            })
            ->afterStateUpdated(fn ($set) => $set('chapter_id', null))
            ->required(),
            // ->disabled(fn ($record) => filled($record))    --disable when editing


        // 3️⃣ Chapter
        Select::make('chapter_id')
            ->label('Chapter')
            ->options(function (callable $get) {
                $subjectId = $get('subject_id');
                if (!$subjectId) return Chapter::pluck('name', 'id');
                return Chapter::where('subject_id', $subjectId)->pluck('name', 'id');
            })
            ->required(),

        // 4️⃣ Subchapter
        TextInput::make('name')
            ->label('Subchapter Name')
            ->maxLength(255)
            ->required(),
    ]);
    
}


    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('chapter.subject.grade.name')
                    ->label('Grade')
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                    ->badge() // makes it a pill-style badge
                    ->color(fn($record) => match ($record->chapter->subject->grade->name ?? null) {
                        'Grade 9'  => 'info',
                        'Grade 10' => 'success',
                        'Grade 11' => 'warning',
                        'Grade 12' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('chapter.subject.name')
                    ->label('Subject')
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                    ->colors(['danger']),

                TextColumn::make('chapter.name')
                    ->label('Chapter')
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                    ->colors(['success']),

                TextColumn::make('name')
                    ->label('Subchapter')
                    ->limit(50)
                    ->sortable()
                    ->searchable()
                   ->colors(['success']),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->since()
                    ->sortable()
                    ->colors(['warning'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->since()
                    ->sortable()
                    ->colors(['warning'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->label('Grade')
                    ->relationship('chapter.subject.grade', 'name')
                    ->options(fn() => \App\Models\Grade::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('All Grades'),

                SelectFilter::make('subject')
                    ->label('Subject')
                    ->options(function (SelectFilter $filter) {
                        $gradeId = $filter->getLivewire()->getTableFilterState('grade')['value'] ?? null;

                        if (!$gradeId) {
                            return \App\Models\Subject::orderBy('name')->pluck('name', 'id');
                        }

                        return \App\Models\Subject::where('grade_id', $gradeId)->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('All Subjects')
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('chapter', function ($query) use ($data) {
                                $query->whereHas('subject', function ($query) use ($data) {
                                    $query->where('id', $data['value']);
                                });
                            });
                        }
                    }),
                SelectFilter::make('chapter_id')
                    ->label('Chapter')
                    ->options(function (SelectFilter $filter) {
                        $subjectId = $filter->getLivewire()->getTableFilterState('subject')['value'] ?? null;

                        if (!$subjectId) {
                            return Chapter::orderBy('name')->pluck('name', 'id');
                        }

                        return Chapter::where('subject_id', $subjectId)->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('All Chapters')
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where('chapter_id', $data['value']);
                        }
                    }),
            ])
            ->deferFilters(false)
            ->defaultGroup('chapter.subject.grade.name')
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
            'index' => ManageSubchapters::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manage Chapters And Sub-Chapters';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sub Chapter';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }
}
