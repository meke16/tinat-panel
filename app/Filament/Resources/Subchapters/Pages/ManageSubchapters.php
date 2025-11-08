<?php

namespace App\Filament\Resources\Subchapters\Pages;

use App\Filament\Resources\Subchapters\SubchapterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubchapters extends ManageRecords
{
    protected static string $resource = SubchapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
