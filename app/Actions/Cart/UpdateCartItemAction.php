<?php

namespace App\Actions\Cart;

use App\Models\UserCartItem;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCartItemAction
{
    use AsAction;

    /**
     * Update cart item quantity.
     *
     * @param UserCartItem $cartItem
     * @param int $quantity
     * @return UserCartItem
     * @throws ValidationException
     */
    public function handle(UserCartItem $cartItem, int $quantity): UserCartItem
    {
        $product = $cartItem->product;

        // Check if requested quantity exceeds stock
        if ($quantity > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.',
            ]);
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }
}
