<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name" => "Alat Laboratorium",
                "code" => "AL",
            ],
            [
                "name" => "Alat Peraga",
                "code" => "AP",
            ],
            [
                "name" => "Alat Kesenian",
                "code" => "AK",
            ],
            [
                "name" => "Perlengkapan Olahraga",
                "code" => "PO",
            ],
            [
                "name" => "Alat Telekomunikasi",
                "code" => "AT",
            ],
            [
                "name" => "Alat Kesehatan",
                "code" => "AK",
            ],
            [
                "name" => "Alat Elektronik",
                "code" => "AE",
            ],
            [
                "name" => "Perlengkapan Accessories",
                "code" => "PA",
            ],
            [
                "name" => "Perlengkapan Ruangan",
                "code" => "PR",
            ],
            [
                "name" => "Perlengkapan Kelas",
                "code" => "AK",
            ],
            [
                "name" => "Papan Tulis",
                "code" => "PT",
            ],
            [
                "name" => "Mebeler",
                "code" => "MB",
            ],
            [
                "name" => "Keamanan",
                "code" => "KM",
            ],
            [
                "name" => "Alat Olahraga",
                "code" => "AO",
            ],
            [
                "name" => "Alat Kebersihan",
                "code" => "AB",
            ],
        ];

        Category::query()->upsert($categories, ['id']);
    }
}
