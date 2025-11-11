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

            TextInput::make('title')
                ->label('Title')
                ->required(),

            TextInput::make('author')
                ->label('Author'),

            TextInput::make('type')
                ->label('Type'),

            TextInput::make('size')
                ->label('Size (bytes)'),


            FileUpload::make('url')
                ->label('Upload File')
                ->directory('uploads/materials')
                ->visibility('public')
                ->disk('public')
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if (!$state) return;

                    // $state is either string (already stored) or UploadedFile object (fresh upload)
                    if (is_object($state)) {
                        // fresh upload
                        $set('title', pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME));
                        $set('type', $state->getClientOriginalExtension());
                        $set('size', round($state->getSize() / 1024 / 1024, 2) . ' MB'); // in MB
                    } else {
                        // already stored file (string path)
                        $path = $state;
                        $set('title', pathinfo($path, PATHINFO_FILENAME));
                        if (Storage::disk('public')->exists($path)) {
                            $set('type', pathinfo($path, PATHINFO_EXTENSION));
                            $size = Storage::disk('public')->size($path);
                            $set('size', round($size / 1024 / 1024, 2) . ' MB');
                        }
                    }
                })

                ->directory('uploads/materials')
                ->visibility('public')
                ->disk('public')
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    if (!$state) return;

                    $fullPath = storage_path('app/public/' . $state);
                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

                    $set('type', $extension);

                    if ($state) {
                        // $state is the filename relative to the disk
                        $path = $state;

                        // Use Storage facade to get size from disk
                        if (Storage::disk('public')->exists($path)) {
                            $size = Storage::disk('public')->size($path);
                            $set('size', round($size / 1024 / 1024, 2) . ' MB'); // MB
                        }
                    }
                }),

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
