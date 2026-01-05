<?php

namespace App\Actions\Cart;

use App\Models\UserCartItem;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCartItemAction
{
    use AsAction;

    /**
     * Delete cart item.
     *
     * @param UserCartItem $cartItem
     * @return bool
     */
    public function handle(UserCartItem $cartItem): bool
    {
        // Check if the cart item exists in the database
        if (!$cartItem->exists) {
            return false;
        }

        return $cartItem->delete();
    }
}
