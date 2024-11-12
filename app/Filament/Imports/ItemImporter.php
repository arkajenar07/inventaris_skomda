<?php

namespace App\Filament\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\Building;
use App\Models\Room;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ItemImporter extends Importer
{
    protected static ?string $model = Item::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->rules(['max:255', 'unique:items,code']),
            ImportColumn::make('name')
                ->rules(['max:255']),
            ImportColumn::make('code_category') // Gantikan category_id dengan code_category
                ->rules(['max:255']),
            ImportColumn::make('code_building') // Gantikan building_id dengan code_building
                ->rules(['max:255']),
            ImportColumn::make('code_room') // Gantikan room_id dengan code_room
                ->rules(['max:255']),
            ImportColumn::make('description')
                ->rules(['max:65535']),
            ImportColumn::make('spesification')
                ->rules(['max:65535']),
            ImportColumn::make('origin_of_acquisition')
                ->rules(['max:65535']),
            ImportColumn::make('series_number')
                ->rules(['max:255']),
            ImportColumn::make('brand')
                ->rules(['max:255']),
            ImportColumn::make('type')
                ->rules(['max:255']),
            ImportColumn::make('color')
                ->rules(['max:255']),
            ImportColumn::make('quantity')
                ->numeric()
                ->rules(['integer', 'min:1']),
            ImportColumn::make('procurement_year')
                ->rules(['integer', 'digits:4']),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('registration_date')
                ->rules(['date']),
            ImportColumn::make('photo')
                ->rules(['max:65535']),
            ImportColumn::make('status')
                ->rules(['max:255']),
            ImportColumn::make('barcode')
                ->rules(['max:65535']),
            ImportColumn::make('note')
                ->rules(['max:65535']),
        ];
    }

    public function resolveRecord(): ?Item
    {
        // Cari category_id berdasarkan code_category
        $category = Category::where('code', $this->data['code_category'])->first();
        $categoryId = $category ? $category->id : null;

        // Cari building_id berdasarkan code_building
        $building = Building::where('code', $this->data['code_building'])->first();
        $buildingId = $building ? $building->id : null;

        // Cari room_id berdasarkan code_room
        $room = Room::where('code', $this->data['code_room'])->first();
        $roomId = $room ? $room->id : null;

        $item = Item::firstOrNew([
            'code' => $this->data['code'], // Matching berdasarkan 'code'
        ]);

        // Set attributes ke item
        $item->fill([
            'name' => $this->data['name'],
            'category_id' => $categoryId, // Menggunakan category_id yang ditemukan
            'building_id' => $buildingId, // Menggunakan building_id yang ditemukan
            'room_id' => $roomId, // Menggunakan room_id yang ditemukan
            'description' => $this->data['description'],
            'spesification' => $this->data['spesification'],
            'origin_of_acquisition' => $this->data['origin_of_acquisition'],
            'series_number' => $this->data['series_number'],
            'brand' => $this->data['brand'],
            'type' => $this->data['type'],
            'color' => $this->data['color'],
            'quantity' => $this->data['quantity'],
            'procurement_year' => $this->data['procurement_year'],
            'price' => $this->data['price'],
            'registration_date' => $this->data['registration_date'],
            'photo' => $this->data['photo'],
            'status' => $this->data['status'],
            'barcode' => $this->data['barcode'],
            'note' => $this->data['note'],
        ]);

        return $item;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your item import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
