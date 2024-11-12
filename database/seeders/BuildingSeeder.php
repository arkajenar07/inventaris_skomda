<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                "name" => "A",
                "code" => "A",
            ],
            [
                "name" => "B",
                "code" => "B",
            ],
        ];

        Building::query()->upsert($buildings, ['id']);
    }
}
