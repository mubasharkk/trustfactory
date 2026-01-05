<?php

namespace App\Actions\Cart;

use App\Models\Product;
use App\Models\UserCartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class AddToCartAction
{
    use AsAction;

    /**
     * Add item to cart.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return UserCartItem
     * @throws ValidationException
     */
    public function handle(int $userId, int $productId, int $quantity): UserCartItem
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            $product = Product::findOrFail($productId);

            // Check if product is in stock
            if ($product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.',
                ]);
            }

            // Check if item already exists in cart
            $cartItem = UserCartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Update quantity if item exists
                $newQuantity = $cartItem->quantity + $quantity;

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
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $cartItem;
        });
    }
}
