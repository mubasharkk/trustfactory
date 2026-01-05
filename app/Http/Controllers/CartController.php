<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Models\UserCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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

        return response()->json([
            'message' => 'Item added to cart successfully',
        ]);
    }

    /**
     * Display the cart view.
     */
    public function index(Request $request)
    {
        $cartItems = UserCartItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return Inertia::render('Cart', [
            'cartItems' => $cartItems,
            'cartCount' => $cartItems->sum('quantity'),
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(UpdateCartItemRequest $request, UserCartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $product = $cartItem->product;

        // Check if requested quantity exceeds stock
        if ($request->quantity > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.',
            ]);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Cart item updated successfully',
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, UserCartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully',
        ]);
    }
}
