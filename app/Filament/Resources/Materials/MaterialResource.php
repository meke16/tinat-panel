<?php

namespace App\Filament\Resources\Materials;

use App\Filament\Resources\Materials\Pages\ManageMaterials;
use App\Models\Chapter;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
            FileUpload::make('url')
                ->label('Upload File')
                ->directory('materials')
                ->visibility('public')
                ->disk('public')
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if (!$state) return;

                    // fresh upload
                    if (is_object($state)) {
                        $set('title', pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME));
                        $set('type', strtoupper($state->getClientOriginalExtension()));
                        $set('size', round($state->getSize() / 1024 / 1024, 2) . ' MB');
                    }
                    // already stored file
                    else {
                        $set('title', pathinfo($state, PATHINFO_FILENAME));

                        if (Storage::disk('public')->exists($state)) {
                            $extension = pathinfo($state, PATHINFO_EXTENSION);
                            $set('type', strtoupper($extension));

                            $size = Storage::disk('public')->size($state);
                            $set('size', round($size / 1024 / 1024, 2) . ' MB');
                        } else {
                            // fallback if file missing
                            $set('type', '');
                            $set('size', '');
                        }
                    }
                })
                ->columnSpanFull(),

            TextInput::make('title')
                ->label('Title')
                ->required(),

            TextInput::make('author')
                ->label('Author'),

            TextInput::make('type')
                ->required()
                ->label('Type'),

            TextInput::make('size')
                ->label('Size (bytes)'),




            Textarea::make('description')
                ->label('Description')
                ->columnSpanFull(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('title')->label('Title')->limit(50)->sortable()->searchable(),
                TextColumn::make('author')->label('Author')->limit(30)->sortable()->searchable(),
                TextColumn::make('type')->label('Type')->sortable(),
                TextColumn::make('size')->label('Size (bytes)')->sortable(),
                BadgeColumn::make('grade.name')->label('Grade')->color('primary')->sortable()->searchable(),
                BadgeColumn::make('subject.name')->label('Subject')->color('success')->sortable()->searchable(),
                BadgeColumn::make('chapter.name')->label('Chapter')->color('warning')->sortable()->searchable(),
            ])
            ->filters([
                SelectFilter::make('grade')->relationship('grade', 'name'),
                SelectFilter::make('subject')->relationship('subject', 'name'),
                SelectFilter::make('chapter')->relationship('chapter', 'name'),
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
            'index' => ManageMaterials::route('/'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Manage Materials';
    }

    public static function getNavigationLabel(): string
    {
        return 'Materials';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }
}
