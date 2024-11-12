<?php

namespace App\Filament\Resources\BorrowedItemResource\Pages;

use App\Filament\Resources\BorrowedItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrowedItems extends ListRecords
{
    protected static string $resource = BorrowedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
