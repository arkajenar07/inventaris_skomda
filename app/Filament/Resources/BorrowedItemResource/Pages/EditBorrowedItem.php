<?php

namespace App\Filament\Resources\BorrowedItemResource\Pages;

use App\Filament\Resources\BorrowedItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrowedItem extends EditRecord
{
    protected static string $resource = BorrowedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
