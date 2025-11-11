<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Models\Subject;
use App\Models\Grade;
use App\Enums\SubjectEnum;
use Doctrine\DBAL\Schema\View;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Unique;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Grade select with badge visualization
                Select::make('grade_id')
                    ->label('Grade')
                    ->required()
                    ->relationship('grade', 'name')
                    ->getOptionLabelFromRecordUsing(fn(Grade $record) => $record->name)
                    ->searchable()
                    ->preload(),

                // Subject select using enum
                Select::make('name')
                    ->label('Subject')
                    ->required()
                    ->options(SubjectEnum::asSelectArray())
                    ->searchable()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, $get) {
                            return $rule->where('grade_id', $get('grade_id'));
                        }
                    ),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            //defaul pagination
            ->defaultPaginationPageOption(25)
            ->modifyQueryUsing(
                fn($query) =>
                $query->orderBy(
                    Grade::select('order')
                        ->whereColumn('grades.id', 'subjects.grade_id')
                )
            )
            ->columns([
                BadgeColumn::make('name')
                    ->label('Subject')
                    ->colors(['info'])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('grade.name')
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


                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->colors(['info'])
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->colors(['info'])
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultGroup('grade.name')
            ->filters([
                SelectFilter::make('grade')
                    ->relationship('grade', 'name'),
                SelectFilter::make('name')
                    ->options(SubjectEnum::asSelectArray()),
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
            'index' => ManageSubjects::route('/'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Manage Subjects';
    }

    public static function getNavigationLabel(): string
    {
        return 'Subject';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
