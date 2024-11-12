<?php

namespace App\Filament\Resources\BorrowedItemResource\Pages;

use App\Filament\Resources\BorrowedItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrowedItem extends CreateRecord
{
    protected static string $resource = BorrowedItemResource::class;
}
