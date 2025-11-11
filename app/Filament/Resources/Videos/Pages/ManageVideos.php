<?php

namespace App\Filament\Resources\Videos\Pages;

use App\Filament\Resources\Videos\VideosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageVideos extends ManageRecords
{
    protected static string $resource = VideosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
