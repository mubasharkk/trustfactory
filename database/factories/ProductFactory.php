<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'category_id' => Category::factory(),
        ];
    }
}
