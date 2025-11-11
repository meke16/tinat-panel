<?php

namespace App\Filament\Resources\Chapters;

use App\Filament\Resources\Chapters\Pages\ManageChapters;
use App\Models\Chapter;
use App\Models\Subject;
use App\Enums\SubjectEnum;
use App\Models\Grade;
use BackedEnum;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        // 1️⃣ Grade Selector
        Select::make('grade_id')
            ->label('Grade')
            ->options(fn() => Grade::orderBy('name')->pluck('name', 'id')->toArray())
            ->live() // reactive in Filament v4 form system
            ->afterStateUpdated(fn(callable $set) => $set('subject_id', null))
            ->required(),

        // 2️⃣ Subject filtered by selected Grade
        Select::make('subject_id')
            ->label('Subject')
            ->options(function (callable $get, callable $set, ?string $state) {
                $gradeId = $get('grade_id');

                // If we’re editing and subject_id exists but grade not yet loaded
                // ensure it loads correctly
                if (!$gradeId && $state) {
                    $subject = Subject::find($state);
                    if ($subject) {
                        $set('grade_id', $subject->grade_id);
                        $gradeId = $subject->grade_id;
                    }
                }

                // If grade not yet chosen, return all subjects or empty list
                if (!$gradeId) {
                    return [];
                }

                return Subject::where('grade_id', $gradeId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();
            })
            ->searchable()
            ->preload()
            ->required(),
        
        TextInput::make('name')
            ->required()
            ->maxLength(255),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            //defaul pagination
            ->defaultPaginationPageOption(25)
            ->columns([
                // Grade badge
                BadgeColumn::make('subject.grade.name')
                    ->label('Grade')
                    ->colors([
                        'primary' => 'Grade 9',
                        'success' => 'Grade 10',
                        'info' => 'Grade 11',
                        'danger' => 'Grade 12',
                        'secondary' => 'Others',
                    ])
                    ->sortable()  // safe; uses Filament's automatic joins
                    ->searchable(),

                // Subject badge using enum
                BadgeColumn::make('subject.name')
                    ->label('Subject')
                    ->colors(['info'])
                    ->sortable()
                    ->searchable(),

                // Chapter name
                TextColumn::make('name')
                    ->label('Chapter Name')
                    ->sortable()
                    ->colors(['success'])
                    ->searchable(),

                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->colors(['warning'])
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                     ->colors(['warning'])
                    ->toggleable(isToggledHiddenByDefault: false),
            ])

            ->defaultGroup('subject.grade.name')
            ->filters([

            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ViewAction::make(),
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
            'index' => ManageChapters::route('/'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Manage Chapters And Sub-Chapters';
    }

    public static function getNavigationLabel(): string
    {
        return 'Chapter';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-circle-stack';
    }
    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
