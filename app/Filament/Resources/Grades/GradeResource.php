<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Models\Grade;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique()
                    ->required(),
                Select::make('order')
                    ->label('Grade Order')
                    ->options([
                        1 => 'first',
                        2 => 'second',
                        3 => 'third',
                        4 => 'fourth',
                        5 => 'Others',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('name')
                    ->label('Grade Name')
                    ->sortable()
                    ->colors([
                        'primary' => 'Grade 9',
                        'success' => 'Grade 10',
                        'info' => 'Grade 11',
                        'danger' => 'Grade 12',
                        'secondary' => 'Others',
                    ])
                    ->searchable(),
                 BadgeColumn::make('order')
                    ->label('Grade Order')
                    ->sortable()
                    ->colors([
                        'primary' => 'Grade 9',
                        'success' => 'Grade 10',
                        'info' => 'Grade 11',
                        'danger' => 'Grade 12',
                        'secondary' => 'Others',
                    ])
                    ->searchable(),
                TextColumn::make('created_at')
                    ->since()
                    ->colors(['info'])
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->since()
                    ->colors(['info'])
                    ->sortable(),
                    ])
            ->defaultSort('order')
            ->filters([
                //
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
            'index' => ManageGrades::route('/'),
        ];
    }
        public static function getNavigationGroup(): ?string
    {
        return 'Manage Grades';
    }

    public static function getNavigationLabel(): string
    {
        return 'Grade';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-academic-cap';
    }
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

}
