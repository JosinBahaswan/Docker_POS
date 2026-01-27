<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Category;
use App\Models\Product;        
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $now = Carbon::now();
        Category::insert([
            [
                'name' => 'Makanan',
                'slug' => 'makanan',
                'description' => 'Kategori untuk semua jenis makanan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'description' => 'Kategori untuk semua jenis minuman',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Product::insert([
            [
                'code' => 'PRD001',
                'name' => 'Nasi Goreng',
                'category_slug' => 'makanan',
                'price' => 15000,
                'stock' => 100,
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'PRD002',
                'name' => 'Es Teh Manis',
                'category_slug' => 'minuman',
                'price' => 8000,
                'stock' => 200,
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'PRD003',
                'name' => 'Mie Ayam',
                'category_slug' => 'makanan',
                'price' => 12000,
                'stock' => 150,
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
