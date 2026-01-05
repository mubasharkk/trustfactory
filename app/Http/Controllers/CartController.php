<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddToCartAction;
use App\Actions\Cart\DeleteCartItemAction;
use App\Actions\Cart\GetUserCartAction;
use App\Actions\Cart\UpdateCartItemAction;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\UserCartItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Add item to cart.
     */
    public function store(StoreCartItemRequest $request)
    {
        AddToCartAction::run(
            $request->user()->id,
            $request->product_id,
            $request->quantity
        );

        return response()->json([
            'message' => 'Item added to cart successfully',
        ]);
    }

    /**
     * Display the cart view.
     */
    public function index(Request $request)
    {
        $cartItems = GetUserCartAction::run($request->user()->id);

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
        $this->authorize('update', $cartItem);

        UpdateCartItemAction::run($cartItem, $request->quantity);

        return response()->json([
            'message' => 'Cart item updated successfully',
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, UserCartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);

        DeleteCartItemAction::run($cartItem);

        return response()->json([
            'message' => 'Item removed from cart successfully',
        ]);
    }
}
