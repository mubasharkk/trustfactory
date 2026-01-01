<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch categories from the API
        $response = Http::get('https://dummyjson.com/products/categories');

        if ($response->successful()) {
            $categories = $response->json();

            foreach ($categories as $categoryData) {
                Category::updateOrCreate(
                    ['slug' => $categoryData['slug']],
                    [
                        'name' => $categoryData['name'],
                        'slug' => $categoryData['slug'],
                    ]
                );
            }

            $this->command->info('Categories seeded successfully!');
        } else {
            $this->command->error('Failed to fetch categories from API.');
        }
    }
}
