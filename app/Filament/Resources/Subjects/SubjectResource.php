<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Models\Subject;
use App\Models\Grade;
use App\Enums\SubjectEnum;
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
use Illuminate\Validation\Rules\Unique;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;

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
                    ->getOptionLabelFromRecordUsing(fn (Grade $record) => $record->name)
                    ->searchable()
                    ->preload(),

                // Subject select using enum
                Select::make('name')
                    ->label('Subject')
                    ->required()
                    ->options(SubjectEnum::asSelectArray())
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
            ->columns([
                // Grade badge
                BadgeColumn::make('grade.name')
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

                // Subject name
                TextColumn::make('name')
                   ->searchable()
                   ->sortable()
                   ->label('Subject'),
               
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultGroup('grade.name')
            ->recordActions([
                ActionGroup::make([
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
            'index' => ManageSubjects::route('/'),
        ];
    }
            public static function getNavigationGroup(): ?string
    {
        return 'Manage Question';
    }

    public static function getNavigationLabel(): string
    {
        return 'Subjects';
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
