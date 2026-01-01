<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch products from the API
        $response = Http::get('https://dummyjson.com/products?limit=200');

        if ($response->successful()) {
            $data = $response->json();
            $products = $data['products'] ?? [];

            foreach ($products as $productData) {
                // Find category by slug
                $categoryId = null;
                if (isset($productData['category'])) {
                    $category = Category::where('slug', Str::slug($productData['category']))->first();
                    $categoryId = $category?->id;
                }

                // Generate slug from title if not present
                $slug = $productData['slug'] ?? Str::slug($productData['title']);

                // Generate SKU if not present
                $sku = $productData['sku'] ?? 'SKU-' . strtoupper(Str::random(8));

                Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $productData['title'],
                        'slug' => $slug,
                        'description' => $productData['description'] ?? null,
                        'image' => $productData['thumbnail'] ?? null,
                        'price' => $productData['price'],
                        'stock_quantity' => $productData['stock'] ?? 0,
                        'sku' => $sku,
                        'category_id' => $categoryId,
                    ]
                );
            }

            $this->command->info('Products seeded successfully!');
        } else {
            $this->command->error('Failed to fetch products from API.');
        }
    }
}
