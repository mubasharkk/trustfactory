<?php

namespace App\Actions\Product;

use App\Models\Category;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCategoriesAction
{
    use AsAction;

    /**
     * Get all categories ordered by name.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function handle()
    {
        return Category::orderBy('name')->get();
    }
}
