<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\ManageQuestions;
use App\Models\Chapter;
use App\Models\Grade;
use App\Models\Question;
use App\Models\Subchapter;
use App\Models\Subject;
use BackedEnum;
use Doctrine\DBAL\Schema\View;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Stringable;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            // Grade select
            Select::make('grade_id')
                ->label('Grade')
                ->options(fn() => Grade::pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('subject_id', null))
                ->afterStateHydrated(function ($state, $get, $set, $record) {
                    if ($record && $record->subchapter_id) {
                        $gradeId = optional(optional(optional($record->subchapter)->chapter)->subject)->grade_id;
                        $set('grade_id', $gradeId);
                    }
                })
                ->required(),

            // Subject select
            Select::make('subject_id')
                ->label('Subject')
                ->options(function ($get) {
                    $gradeId = $get('grade_id');
                    if ($gradeId) {
                        return Subject::where('grade_id', $gradeId)->pluck('name', 'id');
                    }
                    return [];
                })
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('chapter_id', null))
                ->afterStateHydrated(function ($state, $get, $set, $record) {
                    if ($record && $record->subchapter_id) {
                        $subjectId = optional(optional($record->subchapter)->chapter)->subject_id;
                        $set('subject_id', $subjectId);
                    }
                })
                ->required(),

            // Chapter select
            Select::make('chapter_id')
                ->label('Chapter')
                ->options(function ($get) {
                    $subjectId = $get('subject_id');
                    if ($subjectId) {
                        return Chapter::where('subject_id', $subjectId)->pluck('name', 'id');
                    }
                    return [];
                })
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('subchapter_id', null))
                ->afterStateHydrated(function ($state, $get, $set, $record) {
                    if ($record && $record->subchapter_id) {
                        $chapterId = optional($record->subchapter)->chapter_id;
                        $set('chapter_id', $chapterId);
                    }
                })
                ->required(),

            // Subchapter select
            Select::make('subchapter_id')
                ->label('Subchapter')
                ->options(function ($get) {
                    $chapterId = $get('chapter_id');
                    if ($chapterId) {
                        return Subchapter::where('chapter_id', $chapterId)->pluck('name', 'id');
                    }
                    return [];
                })
                ->afterStateHydrated(function ($state, $get, $set, $record) {
                    if ($record && $record->subchapter_id) {
                        $set('subchapter_id', $record->subchapter_id);
                    }
                })
                ->required(),




            // Question content
            RichEditor::make('question_text')
                ->label('Question')
                ->required()
                ->columnSpanFull()
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('questions'),

            RichEditor::make('explanation')
                ->label('Explanation')
                ->required()
                ->columnSpanFull()
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('questions'),

            // Options repeater with RichEditor
            Repeater::make('options')
                ->label('Options')
                ->relationship('options')
                ->required()
                ->createItemButtonLabel('Add Option')
                ->minItems(2)
                ->schema([
                    Hidden::make('order_index')
                        ->default(fn($get) => (int) count($get('options') ?? [])),

                    RichEditor::make('text')
                        ->label('Option Text')
                        ->required()
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('questions')
                        ->columnSpan(2),
                ])
                ->columns(1)
                ->reactive()
                ->orderable('order_index'),

            // Correct answer as radio buttons - stores index in answerIndex
            Radio::make('answerIndex')
                ->label('Correct Answer')
                ->options(function ($get) {
                    $options = $get('options') ?? [];

                    $radioOptions = [];
                    $i = 0;

                    foreach ($options as $option) {
                        if (!empty($option['text'])) {
                            $radioOptions[$i] = "Option " . ($i + 1);
                            $i++;
                        }
                    }

                    return $radioOptions;
                })
                ->required()
                ->columns(1)
                ->gridDirection('row'),

            TextInput::make('tags')
                ->label('Tags')
                ->placeholder('comma,separated,tags'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('subchapter.chapter.subject.grade.name')
                    ->label('Grade')
                    ->colors([
                        'primary',
                    ])
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('subchapter.chapter.subject.name')
                    ->label('Subject')
                    ->colors([
                        'success',
                    ])
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('subchapter.chapter.name')
                    ->label('Chapter')
                    ->colors([
                        'warning',
                    ])
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subchapter.name')
                    ->label('Subchapter')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('question_text')
                    ->label('Question')
                    ->limit(100)
                    ->html(),

                BadgeColumn::make('options.text')
                    ->label('Options')
                    ->limit(100)
                    ->colors(['warning'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->map(fn($option) => strip_tags($option['text'] ?? ''))->implode(' | ');
                        }
                        return strip_tags($state ?? '');
                    }),

                BadgeColumn::make('answerIndex')
                    ->label('Answer Index')
                    ->colors(['success'])
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->label('Grade')
                    ->options(fn() => \App\Models\Grade::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('All Grades')
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('subchapter.chapter.subject', function ($query) use ($data) {
                                $query->whereHas('grade', function ($query) use ($data) {
                                    $query->where('id', $data['value']);
                                });
                            });
                        }
                    }),

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
                            $query->whereHas('subchapter.chapter', function ($query) use ($data) {
                                $query->whereHas('subject', function ($query) use ($data) {
                                    $query->where('id', $data['value']);
                                });
                            });
                        }
                    }),

                SelectFilter::make('chapter')
                    ->label('Chapter')
                    ->options(function (SelectFilter $filter) {
                        $subjectId = $filter->getLivewire()->getTableFilterState('subject')['value'] ?? null;

                        if (!$subjectId) {
                            return \App\Models\Chapter::orderBy('name')->pluck('name', 'id');
                        }

                        return \App\Models\Chapter::where('subject_id', $subjectId)->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('All Chapters')
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('subchapter', function ($query) use ($data) {
                                $query->where('chapter_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('subchapter_id')
                    ->label('Subchapter')
                    ->options(function (SelectFilter $filter) {
                        $chapterId = $filter->getLivewire()->getTableFilterState('chapter')['value'] ?? null;

                        if (!$chapterId) {
                            return \App\Models\Subchapter::orderBy('name')->pluck('name', 'id');
                        }

                        return \App\Models\Subchapter::where('chapter_id', $chapterId)->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('All Subchapters'),
            ])
            ->recordActions([
               ActionGroup::make([
                   ViewAction::make(),
                   EditAction::make(),
                   DeleteAction::make(),
               ])
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
            'index' => ManageQuestions::route('/'),
        ];
    }


            public static function getNavigationGroup(): ?string
    {
        return 'Manage Question';
    }

    public static function getNavigationLabel(): string
    {
        return 'Question';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-question-mark-circle';
    }
    public static function getNavigationSort(): ?int
    {
        return 5;
    }

}
