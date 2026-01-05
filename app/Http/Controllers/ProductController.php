<?php

namespace App\Http\Controllers;

use App\Actions\Product\GetCategoriesAction;
use App\Actions\Product\GetProductsAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category');

        return Inertia::render('Shop', [
            'products' => GetProductsAction::run($categoryId),
            'categories' => GetCategoriesAction::run(),
            'selectedCategory' => $categoryId,
        ]);
    }
}
