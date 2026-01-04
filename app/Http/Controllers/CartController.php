<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Models\Product;
use App\Models\UserCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Add item to cart.
     */
    public function store(StoreCartItemRequest $request)
    {
        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Check if product is in stock
        if ($product->stock_quantity < $request->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.',
            ]);
        }

        // Check if item already exists in cart
        $cartItem = UserCartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity if item exists
            $newQuantity = $cartItem->quantity + $request->quantity;

            // Check if new quantity exceeds stock
            if ($newQuantity > $product->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Cannot add more items. Maximum available: ' . $product->stock_quantity . '. You already have ' . $cartItem->quantity . ' in your cart.',
                ]);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = UserCartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        // Get updated cart count
        $cartCount = UserCartItem::where('user_id', $user->id)->sum('quantity');

        return response()->json([
            'message' => 'Item added to cart successfully',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Get cart count for the authenticated user.
     */
    public function count()
    {
        $user = Auth::user();
        $cartCount = UserCartItem::where('user_id', $user->id)->sum('quantity');

        return response()->json([
            'cart_count' => $cartCount,
        ]);
    }
}
