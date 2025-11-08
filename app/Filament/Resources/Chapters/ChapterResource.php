<?php

namespace App\Filament\Resources\Chapters;

use App\Filament\Resources\Chapters\Pages\ManageChapters;
use App\Models\Chapter;
use App\Models\Subject;
use App\Enums\SubjectEnum;
use App\Models\Grade;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            // 1️⃣ Grade Selector
            Select::make('grade_id')
                ->label('Grade')
                ->options(Grade::pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn ($set) => $set('subject_id', null))
                ->required(),

            // 2️⃣ Subject filtered by Grade
            Select::make('subject_id')
                ->label('Subject')
                ->options(function (callable $get) {
                    $gradeId = $get('grade_id');

                    if (!$gradeId) {
                        return Subject::pluck('name', 'id');
                    }

                    return Subject::where('grade_id', $gradeId)
                        ->pluck('name', 'id');
                })
                ->reactive()
                ->required()
                ->searchable(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->sortable()
                    ->searchable(),

                // Subject badge using enum
                BadgeColumn::make('subject.name')
                    ->label('Subject')
                    ->color(fn (string $state): string => match ($state) {
                        SubjectEnum::MATHEMATICS->value => 'primary',
                        SubjectEnum::ENGLISH->value => 'info',
                        SubjectEnum::HISTORY->value => 'warning',
                        SubjectEnum::GEOGRAPHY->value => 'danger',
                        SubjectEnum::PHYSICS->value => 'primary',
                        SubjectEnum::CHEMISTRY->value => 'success',
                        SubjectEnum::BIOLOGY->value => 'info',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->searchable(),

                // Chapter name
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Chapter Name'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('subject.grade.name')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
        return 'Manage Question';
    }

    public static function getNavigationLabel(): string
    {
        return 'Chapters';
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