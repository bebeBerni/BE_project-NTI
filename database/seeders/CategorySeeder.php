<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Vývoj softvéru',
            'AI a dátové technológie',
            'Webové aplikácie',
            'Herný vývoj',
            'IoT a embedded systémy',
            'Kvalifikačný stack 01',
            'Kvalifikačný stack 02',
            'Kvalifikačný stack 03',
            'Kvalifikačný stack 04',
            'Kvalifikačný stack 05',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
