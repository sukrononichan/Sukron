<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Laptop Gaming',
                'description' => 'Laptop gaming dengan spesifikasi tinggi',
                'price' => 15000000,
                'image' => 'laptop-gaming.jpg',
                'category_id' => 1,
                'is_publish' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kemeja Formal',
                'description' => 'Kemeja formal untuk acara resmi',
                'price' => 250000,
                'image' => 'kemeja-formal.jpg',
                'category_id' => 2,
                'is_publish' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Snack Box',
                'description' => 'Paket snack untuk acara',
                'price' => 50000,
                'image' => 'snack-box.jpg',
                'category_id' => 3,
                'is_publish' => false,
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('products')->insert($products);
    }
}