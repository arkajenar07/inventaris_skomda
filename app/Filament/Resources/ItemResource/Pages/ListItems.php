<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Imports\ItemImporter;
use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(ItemImporter::class)
                ->icon('heroicon-o-document-arrow-down'),
        ];
    }
}
