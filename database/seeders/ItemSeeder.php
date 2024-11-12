<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Category;
use App\Models\item;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('inventory.json'));
        $data = json_decode($json);

        $user = User::query()->select(['id', 'created_at'])->oldest()->first();
        foreach ($data->inventories as $inventory) {
            $category = Category::query()
                ->select(['id', 'name'])
                ->where('name', $inventory->category)
                ->first();

            try {
                $building = Building::query()->firstOrCreate([
                        'name' => $inventory->building ?? 'A',
                    ], [
                        'name' => $inventory->building ?? 'A',
                    ]);
            } catch (\Exception $exception) {
                dd($inventory, $category);
            }

            $room = Room::query()->where('name', $inventory->room)->first();
            $room->update(['building_id' => $building->id]);

            $data = [
                'created_by' => $user->id ?? null,
                'code' => $inventory->code,
                'name' => $inventory->name,
                'category_id' => $category->id ?? null,
                'description' => $inventory->description ?? null,
                'origin_of_acquisition' => $inventory->origin_of_acquisition ?? '',
                'building_id' => $building->id,
                'room_id' => $room->id,
                'barcode' => $inventory->barcode,
                'note' => $inventory->note,
            ];
            item::query()->create($data);
        }
    }
}
