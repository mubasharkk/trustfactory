<?php

namespace App\Actions\Product;

use App\Models\Product;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProductsAction
{
    use AsAction;

    /**
     * Get products with optional category filter.
     *
     * @param int|null $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle(?int $categoryId = null)
    {
        $query = Product::with('category');

        // Filter by category if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
